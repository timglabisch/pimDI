<?php

class DI_reflectionMethod extends ReflectionMethod {

    private static $annotationCache;

    public function parseTestMethodAnnotations($className, $methodName = '')
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
