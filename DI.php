<?php
namespace de\any;

require_once __DIR__.'/DI/binder.php';
require_once __DIR__.'/DI/ReflectionAnnotation.php';

class di {

    private $binderRepository = null;
    private $instances = array();

    public function createInstanceFromClassname($classname) {
        if(!class_exists($classname))
            throw new Exception('class with classname '. $classname.' not found');

        $reflectionClass = new \ReflectionClass($classname);
        return $this->createInstance($reflectionClass);
    }

    private function createInstance(\ReflectionClass $reflection) {

        if(!$reflection->hasMethod('__construct'))
            return $reflection->newInstance();

        $reflectionMethod = $reflection->getConstructor();
        $args = $this->getInjectedArgs($reflectionMethod);
        return $reflection->newInstanceArgs($args);
    }

    public function get($interface, $concern='') {

        $binding = $this->getBinderRepository()->getBinding($interface, $concern);

        $reflection = new \ReflectionClass($binding->getInterfaceImpl());

        if(!$reflection->implementsInterface($interface))
            throw new Exception($reflection->getName() .' must implement '. $interface);

        if($binding->isShared() && isset($this->instances[$interface .'|'. $concern]))
            return $this->instances[$interface .'|'. $concern];
        
        $instance = $this->createInstance($reflection);

        if($binding->isShared())
            $this->instances[$interface .'|'. $concern] = $instance;

        $this->injectSetters($instance, $reflection);

        return $instance;
    }

    private function getInjectedArgs(\ReflectionMethod $reflectionMethod) {
        $params = $reflectionMethod->getParameters();
        $annotationStrings = di\ReflectionAnnotation::parseMethodAnnotations($reflectionMethod);
        $annotations = $annotationStrings['inject'];

        $args = array();

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
     * @return binderRepository
     */
    public function getBinderRepository()
    {
        if($this->binderRepository === null)
            $this->binderRepository = new di\binder\repository();
        
        return $this->binderRepository;
    }

}