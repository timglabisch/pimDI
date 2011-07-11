<?php

require_once __DIR__.'/DI/binder.php';
require_once __DIR__ . '/DI/ReflectionAnnotation.php';
require_once __DIR__.'/DI/binderRepository.php';

class di {

    static $annotationCache;
    private $binderRepository = null;
    var $instances = array();

    private function createInstance(ReflectionClass $reflection) {


        if($reflection->hasMethod('__construct')) {

            $reflectionMethod = $reflection->getConstructor();
            $params = $reflectionMethod->getParameters();
            $annotationStrings = ReflectionAnnotation::parseMethodAnnotations($reflectionMethod);
            $annotations = $annotationStrings['inject'];

            $instanceParams = array();

            for($i=0;count($params) > $i; $i++) {
                $concern = (isset($annotations[$i])?$annotations[$i]:'');
                $instanceParams[] = $this->get($params[$i]->getClass()->getName(), $concern);
            }

            return $reflection->newInstanceArgs($instanceParams);
        }

        return $reflection->newInstance();
    }


    public function get($interface, $concern='') {

        $binding = $this->getBinderRepository()->getBinding($interface, $concern);

        $reflection = new ReflectionClass($binding->getInterfaceImpl());

        if(!$reflection->implementsInterface($interface))
            throw new Exception($reflection->getName() .' must implement '. $interface);

        if($binding->getShared() && isset($this->instances[$interface .'|'. $concern]))
            return $this->instances[$interface .'|'. $concern];
        
        $instance = $this->createInstance($reflection);

        if($binding->getShared())
            $this->instances[$interface .'|'. $concern] = $instance;

        $this->injectSetters($instance, $reflection);

        return $instance;
    }

    public function injectSetters($instance, ReflectionClass $reflection) {
        foreach($reflection->getMethods() as $reflectionMethod) {

            if($reflectionMethod->isConstructor() || $reflectionMethod->isDestructor() || $reflectionMethod->isStatic())
                continue;

            $annotationStrings = ReflectionAnnotation::parseMethodAnnotations($reflectionMethod);

            if(!isset($annotationStrings['inject']))
                continue;

            $params = $reflectionMethod->getParameters();
            if(!count($params))
                throw new Exception('parameters cant be empty');

            $annotations = $annotationStrings['inject'];

            $args = array();

            for($i=0;count($params) > $i; $i++) {
                $concern = (isset($annotations[$i])?$annotations[$i]:'');
                $args[] = $this->get($params[$i]->getClass()->getName(), $concern);
            }

            $this->callMethod($instance, $reflectionMethod->name, $args);
        }
    }

    public function callMethod($instance, $methodName, $args) {
        switch(count($args)) {
            case 0:
                    $instance->$methodName();
                break;
            case 1:
                   $instance->$methodName($args[0]);
                break;
            default:
                    call_user_func_array(array($instance, $methodName), $args);
                break;
            }
    }

    public function bind($interfaceName) {
        $binder = new binder($interfaceName);
        $this->getBinderRepository()->addBinding($binder);
        return $binder;
    }

    public function setBinderRepository($binderRepository)
    {
        $this->binderRepository = $binderRepository;
    }

    /**
     * @return binderRepository
     */
    public function getBinderRepository()
    {
        if($this->binderRepository === null)
            $this->binderRepository = new binderRepository();
        
        return $this->binderRepository;
    }

}