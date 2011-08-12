<?php

namespace de\any\di;

class ReflectionAnnotation {

    private static $annotationCache;

    public static function parseMethodAnnotations(\ReflectionMethod $refelectionMethod) {
        if (!isset(self::$annotationCache[$refelectionMethod->class . '::' . $refelectionMethod->name])) {
            self::$annotationCache[$refelectionMethod->class . '::' . $refelectionMethod->name] = self::parseAnnotations($refelectionMethod->getDocComment());
        }

        return self::$annotationCache[$refelectionMethod->class . '::' . $refelectionMethod->name];
    }

    public static function parsePropertyAnnotations(\ReflectionProperty $refelectionProperty) {
        if (!isset(self::$annotationCache['property::'.$refelectionProperty->class . '::' . $refelectionProperty->name])) {
            self::$annotationCache['property::'.$refelectionProperty->class . '::' . $refelectionProperty->name] = self::parseAnnotations($refelectionProperty->getDocComment());
        }

        return self::$annotationCache['property::'.$refelectionProperty->class . '::' . $refelectionProperty->name];
    }

    private static function parseAnnotations($docblock) {
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
