<?php

require_once __DIR__.'/DI/binder.php';

class di {

    static $annotationCache;
    var $bindings = array();

    private function _diVerifInterfaceExists($interface) {
        if(!interface_exists('\\'.$interface))
            throw new Exception('interface '.$interface.' does not exists.');
    }

    public function __get($interface) {
        $this->_diVerifInterfaceExists($interface);

        if(!isset($this->bindings[$interface.'|']))
            throw new Exception('Interfaces "'.$interface.'" is not mapped to a class');

        $className = $this->bindings[$interface.'|']->getInterfaceImpl();

        $reflection = new ReflectionClass($className);
        $instance = new $className;


        foreach($reflection->getMethods() as $method) {
            $annotationStrings = self::parseTestMethodAnnotations($method->class, $method->name);
            if(!isset($annotationStrings['method'], $annotationStrings['method']['inject']))
                continue;

            $annotations = explode(',', implode(',',$annotationStrings['method']['inject']));
            $annotations = array_map('trim', $annotations);

            if(count($annotations) != 1)
                throw new Exception('not supportet atm.');

            $instance->{$method->name}($this->__get($annotations[0]));
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

    public function bind($interfaceName) {
        $binder =  new binder($interfaceName);
        $this->bindings[$binder->getHashKey()] = $binder;
        return $binder;
    }

}