<?php

class di {

    var $services;
    static $annotationCache;

    private function _diVerifInterfaceExists($interface) {
        if(!interface_exists('\\'.$interface))
            throw new Exception('interface '.$interface.' does not exists.');
    }

    private function _diInject($class) {

        $reflection = new ReflectionClass($class);


        return $class;
    }

    public function __get($interface) {
        $this->_diVerifInterfaceExists($interface);

        if(!isset($this->services[$interface]))
            throw new Exception('Interfaces is not mapped to a class');

        $className = $this->services[$interface];

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

    public function __set($interface, $class) {
        $this->_diVerifInterfaceExists($interface);

        $this->services[$interface] = $class;
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

}