<?php
namespace de\any;
use \de\any\di\reflection\klass\standard as diReflectionClass;

require_once __DIR__.'/DI/binder.php';
require_once __DIR__.'/DI/ReflectionAnnotation.php';
require_once __DIR__ . '/iDi.php';
require_once __DIR__.'/DI/exception.php';
require_once __DIR__.'/DI/reflection/iKlass.php';
require_once __DIR__.'/DI/reflection/klass/standard.php';
require_once __DIR__.'/DI/reflection/iMethod.php';
require_once __DIR__.'/DI/reflection/method/standard.php';
require_once __DIR__.'/DI/reflection/iParam.php';
require_once __DIR__.'/DI/reflection/param/standard.php';
require_once __DIR__.'/DI/iRunable.php';
require_once __DIR__.'/DI/iCache.php';
require_once __DIR__.'/DI/iDecorateable.php';
require_once __DIR__.'/DI/iRepository.php';
require_once __DIR__.'/DI/repository/standard.php';


class di implements iDi {

    private $binderRepository = null;
    private $lock = array();

    public function __construct() {
        // default bindings
        $this->bind('\de\any\iDi')->to($this);
    }

    public function createInstanceFromClassname($classname) {
        $reflectionClass = new diReflectionClass($classname);
        return $this->createInstance($reflectionClass);
    }

    private function createInstance(\de\any\di\reflection\iKlass $reflection, $args=array()) {
        if(!$reflection->hasMethod('__construct'))
            return $reflection->newInstance();

        $reflectionMethod = $reflection->getConstructor();

        if($reflectionMethod->getInject())
            $args = array_merge($args, $this->getInjectedMethodArgs($reflectionMethod));

        $instance = $reflection->newInstanceArgs($args);

        return $instance;
    }

    private function getByBinding($binding, $args=array(), $decorated=false) {

        if($binding->isShared() && $binding->getInstance())
            return $binding->getInstance();

        $reflection = new diReflectionClass($binding->getInterfaceImpl());

        if(!$binding->isRepository()) {
            if(!$reflection->implementsInterface($binding->getInterfaceName()))
                throw new \Exception($reflection->getClassname() .' must implement '. $binding->getInterfaceName());
        }

        $hashKey = $binding->getHashKey();
        
        if(isset($this->lock[$hashKey]))
            throw new \de\any\di\exception\circular('a', 'b');

        $this->lock[$hashKey] = true;

        $instance = $this->createInstance($reflection, $args);

        if($binding->isShared())
            $binding->setInstance($instance);

        $this->injectSetters($instance, $reflection);
        $this->injectProperties($instance, $reflection);

        unset($this->lock[$hashKey]);

        if(!$decorated) {
            $decorators = $this->getBinderRepository()->getBindingDecorators($binding->getInterfaceName(), $binding->getConcern());
            if(count($decorators)) {
                foreach($decorators as $decorator) {

                    $decoratedInstance = $instance;

                    $instance = $this->getByBinding($decorator, array($decoratedInstance), true);

                    if(!($instance instanceof \de\any\di\iDecorateable))
                        throw new \Exception('class '.get_class($instance).' must implement de\any\di\iDecorateable');

                    $instance->setDecotaredClass($decoratedInstance);
                }
            }
        }

        return $instance;
    }
    
    public function get($interface, $concern='', $args=array()) {
        $binding = $this->getBinderRepository()->getBinding($interface, $concern);
        return $this->getByBinding($binding, $args);
    }

    private function getInjectedMethodArgs(\de\any\di\reflection\iMethod $reflectionMethod) {

        $params = $reflectionMethod->getParameters();

        if(!$params)
            return array();

       foreach($params as $param) {

           if(!$param->getInject()) {
               $args[] = null;
               continue;
           }

           $args[] = $this->get($param->getInterface(), $param->getConcern());
       }

        return $args;
    }

    private function injectSetters($instance, \de\any\di\reflection\iKlass $reflection) {
        $methods = $reflection->getSetterMethodsAnnotatedWith('inject');

        if(!$methods)
            return;

        foreach($methods as $reflectionMethod) {
            $args = $this->getInjectedMethodArgs($reflectionMethod);
            $reflectionMethod->invokeArgs($instance, $args);
        }
    }

    private function injectProperties($instance, \de\any\di\reflection\iKlass $reflection) {
        $injProp = $reflection->getInjectProperties();

        if(!$injProp)
            return;

        foreach($injProp as $name => $reflectionProperty) {
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
            $this->binderRepository = new di\binder\repository($this);
        
        return $this->binderRepository;
    }

    function run(di\iRunable $runable) {
        $reflection = new diReflectionClass(get_class($runable));

        $this->injectSetters($runable, $reflection);
        $this->injectProperties($runable, $reflection);

        $runable->run();

        return $runable;
    }

    function justInject($runable) {
        $reflection = new diReflectionClass(get_class($runable));

        $this->injectSetters($runable, $reflection);
        $this->injectProperties($runable, $reflection);

        return $runable;
    }
}