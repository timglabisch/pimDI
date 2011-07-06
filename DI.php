<?php

require_once __DIR__.'/DI/binder.php';

class di {

    static $annotationCache;
    var $bindings = array();
    var $unknownBindings = array();
    var $instances = array();

    private function _diVerifInterfaceExists($interface) {
        if(!interface_exists('\\'.$interface))
            throw new Exception('interface '.$interface.' does not exists.');
    }

    public function get($interface, $concern='') {

        if(!interface_exists($interface)) {
            throw new Exception('Interface '. $interface .' must Exists.');
        }

        $this->knowBindings();

        $this->_diVerifInterfaceExists($interface);

        if(!isset($this->bindings[$interface.'|'.$concern]))
            throw new Exception('Interfaces "'.$interface.'" with concern "'.$concern.'" is not mapped to a class');

        $binding = $this->bindings[$interface.'|'.$concern];
        
        $className = $binding->getInterfaceImpl();
        $reflection = new ReflectionClass($className);

        if(!$binding->getShared())
            if(method_exists($className, '__construct'))
                $instance = call_user_func_array(array($className, '__construct'), array());
            else
                $instance = new $className();
        else
        {
            if(isset($this->instances[$interface.'|'.$concern]))
                $instance = $this->instances[$interface.'|'.$concern];
            else {
                 if(method_exists($className, '__construct'))
                    $instance = $this->instances[$interface.'|'.$concern] = call_user_func_array(array($className, '__construct'), array());
                else
                    $instance = new $className();
            }
        }

        foreach($reflection->getMethods() as $method) {
            $annotationStrings = self::parseTestMethodAnnotations($method->class, $method->name);
            if(!isset($annotationStrings['method'], $annotationStrings['method']['inject']))
                continue;

            $annotations = explode(',', implode(',',$annotationStrings['method']['inject']));
            $annotations = array_map('trim', $annotations);

            if(count($annotations) != 1)
                throw new Exception('not supportet atm.');

            $instance->{$method->name}($this->get($annotations[0]));
        }

        return $instance;
    }

    public static function parseTestMethodAnnotations($className, $methodName = '')
    {
        if (!isset(self::$annotationCache[$className])) {
            $class = new ReflectionClass($className);
            self::$annotationCache[$className] = self::parseAnnotations($class->getDocComment());
        }

        if (!empty($methodName) && !isset(self::$annotationCache[$className . '::' . $methodName])) {
            $method = new ReflectionMethod($className, $methodName);
            self::$annotationCache[$className . '::' . $methodName] = self::parseAnnotations($method->getDocComment());
        }

        return array(
          'class'  => self::$annotationCache[$className],
          'method' => !empty($methodName) ? self::$annotationCache[$className . '::' . $methodName] : array()
        );
    }

    private static function parseAnnotations($docblock)
    {
        $annotations = array();

        if (preg_match_all('/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m', $docblock, $matches)) {
            $numMatches = count($matches[0]);

            for ($i = 0; $i < $numMatches; ++$i) {
                $annotations[$matches['name'][$i]][] = $matches['value'][$i];
            }
        }

        return $annotations;
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