<?php
namespace de\any;
use \de\any\di\reflection\klass\standard as diReflectionClass;

require_once __DIR__.'/DI/binder.php';
require_once __DIR__.'/DI/ReflectionAnnotation.php';
require_once __DIR__ . '/iDi.php';
require_once __DIR__.'/DI/exception.php';

require_once __DIR__.'/DI/reflection/iKlass.php';
require_once __DIR__.'/DI/reflection/klass/standard.php';

require_once __DIR__.'/DI/iRunable.php';


class di implements iDi {

    private $binderRepository = null;
    private $lock = array();

    public function createInstanceFromClassname($classname) {
        if(!class_exists($classname))
            throw new Exception('class with classname '. $classname.' not found');

        $reflectionClass = new diReflectionClass($classname);
        return $this->createInstance($reflectionClass);
    }

    private function createInstance(\de\any\di\reflection\iKlass $reflection, $args=array()) {
        if(!$reflection->hasMethod('__construct'))
            return $reflection->newInstance();

        $reflectionMethod = $reflection->getConstructor();
        $annotationStrings = di\ReflectionAnnotation::parseMethodAnnotations($reflectionMethod);

        $args = array_merge($args, $this->getInjectedMethodArgs($reflectionMethod, $annotationStrings));
        return $reflection->newInstanceArgs($args);
    }

    private function getByBinding($binding, $args=array(), $decorated=false) {

        if($binding->isShared() && $binding->getInstance())
            return $binding->getInstance();

        $reflection = new diReflectionClass($binding->getInterfaceImpl());

        if(!$reflection->implementsInterface($binding->getInterfaceName()))
            throw new \Exception($reflection->getName() .' must implement '. $binding->getInterfaceName());

        if(isset($this->lock[$binding->getHashKey()]))
            throw new \de\any\di\exception\circular('a', 'b');

        $this->lock[$binding->getHashKey()] = true;

        $instance = $this->createInstance($reflection, $args);

        if($binding->isShared())
            $binding->setInstance($instance);

        $this->injectSetters($instance, $reflection);
        $this->injectProperties($instance, $reflection);

        unset($this->lock[$binding->getHashKey()]);

        if(!$decorated) {
            $decorators = $this->getBinderRepository()->getBindingDecorators($binding->getInterfaceName(), $binding->getConcern());
            if(count($decorators)) {
                foreach($decorators as $decorator) {
                    $instance = $this->getByBinding($decorator, array($instance), true);
                }
            }
        }

        return $instance;
    }

    public function get($interface, $concern='', $args=array()) {
        $binding = $this->getBinderRepository()->getBinding($interface, $concern);

        return $this->getByBinding($binding, $args);
    }

    private function getInjectedMethodArgs(\ReflectionMethod $reflectionMethod, $annotationStrings) {

        if(!isset($annotationStrings['inject']))
                return array();

        $annotations = $annotationStrings['inject'];

        $params = $reflectionMethod->getParameters();

        $args = array();
        for($i=0;count($params) > $i; $i++) {
            $concern = (isset($annotations[$i])?$annotations[$i]:'');
            $args[] = $this->get($params[$i]->getClass()->getName(), $concern);
        }

        return $args;
    }

    private function injectSetters($instance, \de\any\di\reflection\iKlass $reflection) {
        $methods = $reflection->getSetterMethodsAnnotatedWith('inject');

        if(!$methods)
            return;

        foreach($methods as $reflectionMethod) {
            $args = $this->getInjectedMethodArgs($reflectionMethod['method'], $reflectionMethod['annotation']);
            $reflectionMethod['method']->invokeArgs($instance, $args);
        }
    }

    private function injectProperties($instance, \de\any\di\reflection\iKlass $reflection) {
        $injProp = $reflection->getInjectProperties();

        if(!$injProp)
            return;

        foreach($reflection->getInjectProperties() as $name => $reflectionProperty) {
            $instance->$name = $this->get($reflectionProperty->getInterfaceName(), $reflectionProperty->getConcern());
        }
    }

    public function bind($interfaceName) {
        $binder = new di\binder($interfaceName);
        $this->getBinderRepository()->addBinding($binder);
        return $binder;
    }

    public function setBinderRepository($binderRepository) {
        $this->binderRepository = $binderRepository;
    }

    /**
     * @return di\binder\repository
     */
    public function getBinderRepository() {
        if($this->binderRepository === null)
            $this->binderRepository = new di\binder\repository();
        
        return $this->binderRepository;
    }

    function run(di\iRunable $runable) {
        $reflection = new diReflectionClass(get_class($runable));

        $this->injectSetters($runable, $reflection);
        $this->injectProperties($runable, $reflection);

        $runable->run();

        return $runable;
    }

}