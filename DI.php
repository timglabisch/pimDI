<?php

require_once __DIR__.'/DI/binder.php';
require_once __DIR__.'/DI/reflection.php';
require_once __DIR__.'/DI/reflectionMethod.php';

class di {

    static $annotationCache;
    var $bindings = array();
    var $unknownBindings = array();
    var $instances = array();

    private function verifInterfaceExists($interface) {
        if(!interface_exists('\\'.$interface))
            throw new Exception('interface '.$interface.' does not exists.');
    }

    private function getHasFromString($interface, $concern='') {
        return $interface.'|'.$concern;
    }

    public function get($interface, $concern='') {

        $this->verifInterfaceExists($interface);
        $this->knowBindings();

        $bindingHash = $this->getHasFromString($interface, $concern);

        if(!isset($this->bindings[$bindingHash]))
            throw new Exception('Interfaces "'.$interface.'" with concern "'.$concern.'" is not mapped to a class');

        $binding = $this->bindings[$bindingHash];
        
        $className = $binding->getInterfaceImpl();
        $reflection = new DI_reflection($className);

        if($binding->getShared() && isset($this->instances[$bindingHash]))
            return $this->instances[$bindingHash];
        
        if(method_exists($className, '__construct')) {

            $reflectionMethod = new DI_reflectionMethod($className, '__construct');
            $params = $reflectionMethod->getParameters();
            $annotationStrings = $reflectionMethod->parseTestMethodAnnotations($className, '__construct');
            $annotations = $annotationStrings['method']['inject'];

            $instanceParams = array();

            $i = 0;
            foreach($params as $v) {
                $concern = (isset($annotations[$i])?$annotations[$i]:'');
                $instanceParams[] = $this->get($v->getClass()->getName(), $concern);
                $i++;
            }

            $instance = $reflection->newInstanceArgs($instanceParams);
        }
        else
            $instance = new $className();

         if($binding->getShared())
            $this->instances[$bindingHash] = $instance;

        foreach($reflection->getMethods() as $method) {
            $reflectionMethod = new DI_reflectionMethod($method->class, $method->name);

            if($reflectionMethod->isConstructor() || $reflectionMethod->isDestructor() || $reflectionMethod->isStatic())
                continue;

            $params = $reflectionMethod->getParameters();

            $annotationStrings = $reflectionMethod->parseTestMethodAnnotations($method->class, $method->name);
            
            if(!isset($annotationStrings['method'], $annotationStrings['method']['inject']))
                continue;

            $annotations = $annotationStrings['method']['inject'];
          
            $instanceParams = array();

            $i = 0;
            foreach($params as $v) {
                $concern = (isset($annotations[$i])?$annotations[$i]:'');
                $instanceParams[] = $this->get($v->getClass()->getName(), $concern);
                $i++;
            }
            
            call_user_func_array(array($instance, $method->name), $instanceParams);
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