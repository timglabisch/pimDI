<?php
namespace de\any;

require_once __DIR__.'/DI/binder.php';
require_once __DIR__.'/DI/ReflectionAnnotation.php';
require_once __DIR__.'/Idi.php';

class di implements iDi {

    private $binderRepository = null;
    private $instances = array();

    public function createInstanceFromClassname($classname) {
        if(!class_exists($classname))
            throw new Exception('class with classname '. $classname.' not found');
        
        $reflectionClass = new \ReflectionClass($classname);
        return $this->createInstance($reflectionClass);
    }

    private function createInstance(\ReflectionClass $reflection, $args=array()) {

        if(!$reflection->hasMethod('__construct'))
            return $reflection->newInstance();

        $reflectionMethod = $reflection->getConstructor();
        $args = array_merge($args, $this->getInjectedArgs($reflectionMethod));
        return $reflection->newInstanceArgs($args);
    }

    private function getByBinding($binding, $args=array(), $decorated=false) {
        $reflection = new \ReflectionClass($binding->getInterfaceImpl());

        if(!$reflection->implementsInterface($binding->getInterfaceName()))
            throw new Exception($reflection->getName() .' must implement '. $binding->getInterfaceName());

        if($binding->isShared() && isset($this->instances[$binding->getInterfaceName() .'|'. $binding->getConcern()]))
            return $this->instances[$binding->getInterfaceName() .'|'. $binding->getConcern()];

        $instance = $this->createInstance($reflection, $args);

        if($binding->isShared())
            $this->instances[$binding->getInterfaceName() .'|'. $binding->getConcern()] = $instance;

        $this->injectSetters($instance, $reflection);

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

    private function getInjectedArgs(\ReflectionMethod $reflectionMethod) {
        $params = $reflectionMethod->getParameters();
        $annotationStrings = di\ReflectionAnnotation::parseMethodAnnotations($reflectionMethod);

        $args = array();

        if(!isset($annotationStrings['inject']))
            return $args;

        $annotations = $annotationStrings['inject'];

        for($i=0;count($params) > $i; $i++) {
            $concern = (isset($annotations[$i])?$annotations[$i]:'');
            $args[] = $this->get($params[$i]->getClass()->getName(), $concern);
        }

        return $args;
    }

    private function injectSetters($instance, \ReflectionClass $reflection) {
        foreach($reflection->getMethods() as $reflectionMethod) {

            if($reflectionMethod->isConstructor() || $reflectionMethod->isDestructor() || $reflectionMethod->isStatic())
                continue;

            $annotationStrings = di\ReflectionAnnotation::parseMethodAnnotations($reflectionMethod);

            if(!isset($annotationStrings['inject']))
                continue;

            $args = $this->getInjectedArgs($reflectionMethod);
            $reflectionMethod->invokeArgs($instance, $args);
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
    public function getBinderRepository()
    {
        if($this->binderRepository === null)
            $this->binderRepository = new di\binder\repository();
        
        return $this->binderRepository;
    }

}