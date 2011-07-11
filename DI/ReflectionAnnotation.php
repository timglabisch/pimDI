<?php

class ReflectionAnnotation {

    private static $annotationCache;

    public static function parseMethodAnnotations(ReflectionMethod $refelectionMethod)
    {
        if (!isset(self::$annotationCache[$refelectionMethod->class . '::' . $refelectionMethod->name])) {
            self::$annotationCache[$refelectionMethod->class . '::' . $refelectionMethod->name] = self::parseAnnotations($refelectionMethod->getDocComment());
        }

        return self::$annotationCache[$refelectionMethod->class . '::' . $refelectionMethod->name];
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
