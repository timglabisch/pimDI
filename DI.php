<?php

require_once __DIR__.'/DI/binder.php';
require_once __DIR__.'/DI/reflection.php';
require_once __DIR__.'/DI/reflectionMethod.php';

class di {

    static $annotationCache;
    var $bindings = array();
    var $unknownBindings = array();
    var $instances = array();

    private function getHasFromString($interface, $concern='') {
        return $interface.'|'.$concern;
    }

    private function createInstance(DI_reflection $reflection) {


        if($reflection->hasMethod('__construct')) {

            $reflectionMethod = $reflection->getConstructor();
            $params = $reflectionMethod->getParameters();
            $annotationStrings = DI_reflectionMethod::parseTestMethodAnnotations($reflection->getName(), '__construct');
            $annotations = $annotationStrings['method']['inject'];

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

        $this->knowBindings();

        $bindingHash = $this->getHasFromString($interface, $concern);

        if(!isset($this->bindings[$bindingHash]))
            throw new Exception('Interfaces "'.$interface.'" with concern "'.$concern.'" is not mapped to a class');

        $binding = $this->bindings[$bindingHash];
        
        $className = $binding->getInterfaceImpl();
        $reflection = new DI_reflection($className);

        if(!$reflection->implementsInterface($interface))
            throw new Exception($className .' must implement '. $interface);

        if($binding->getShared() && isset($this->instances[$bindingHash]))
            return $this->instances[$bindingHash];
        
        $instance = $this->createInstance($reflection);

         if($binding->getShared())
            $this->instances[$bindingHash] = $instance;

        foreach($reflection->getMethods() as $reflectionMethod) {

            if($reflectionMethod->isConstructor() || $reflectionMethod->isDestructor() || $reflectionMethod->isStatic())
                continue;

            $params = $reflectionMethod->getParameters();

            $annotationStrings = DI_reflectionMethod::parseTestMethodAnnotations($reflectionMethod->class, $reflectionMethod->name);
            
            if(!isset($annotationStrings['method'], $annotationStrings['method']['inject']))
                continue;

            $annotations = $annotationStrings['method']['inject'];
          
            $instanceParams = array();

            for($i=0;count($params) > $i; $i++) {
                $concern = (isset($annotations[$i])?$annotations[$i]:'');
                $instanceParams[] = $this->get($params[$i]->getClass()->getName(), $concern);
            }

            switch(count($instanceParams)) {
            case 0:
                    throw new Exception('wtf?');
                break;
            case 1:
                   $instance->{$reflectionMethod->name}($instanceParams[0]);
                break;
            default:
                    call_user_func_array(array($instance, $reflectionMethod->name), $instanceParams);
                break;
            }
        }


        return $instance;
    }

    public function knowBindings() {

        if(!count($this->unknownBindings))
            return;

        foreach($this->unknownBindings as $key => $unknownBinding) {
            $this->bindings[$unknownBinding->getHashKey()] = $unknownBinding;
            unset($this->unknownBindings[$key]);
        }
    }

    public function bind($interfaceName) {
        $binder =  new binder($interfaceName);
        $this->unknownBindings[] = $binder;
        return $binder;
    }

}