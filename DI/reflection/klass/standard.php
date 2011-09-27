<?php
namespace de\any\di\reflection\klass;

class standard implements \de\any\di\reflection\iKlass  {

    private $classname;
    private $methods = null;
    private $reflectionClass = null;
    private $properties;
    private $methodsAnnotatedWith = array();
    private $injectProperties = array();
    public static $cache;

    public function __construct($classname) {
        $this->setClassname($classname);
    }
    
    public function newInstance() {
        $classname = $this->getClassname();
        return new $classname;
    }

    public function newInstanceArgs(array $args=array()) {

        $argsLength = count($args);

        $classname = $this->getClassname();

        switch($argsLength) {
            case 0:
                return $this->newInstance();
                break;
            case 1:
                return new $classname($args[0]);
                break;
            case 2:
                return new $classname($args[0], $args[1]);
                break;
            case 3:
                return new $classname($args[0], $args[1], $args[2]);
                break;
            case 4:
                return new $classname($args[0], $args[1], $args[2], $args[3]);
                break;
            case 5:
                return new $classname($args[0], $args[1], $args[2], $args[3], $args[4]);
                break;
            case 6:
                return new $classname($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
                break;
        }

        return $this->getReflectionClass()->newInstanceArgs($args);
    }

    public function setClassname($classname) {
        $this->classname = $classname;
    }

    public function hasMethod($method) {
        $methods = $this->getMethods();

        return isset($methods[$method]);
    }

    public function getMethods() {
        if($this->methods == null) {
            $this->methods = $this->getCache()->fetch('reflection|'.$this->getClassname().'|methods');

            if($this->methods === false) {
                $methods = $this->getReflectionClass()->getMethods();
                
                $this->methods = array();

                foreach($methods as $method) {

                    $dicMethod = new \de\any\di\reflection\method\standard($method->getName());
                    $dicMethod->setParamsByReflectionMethod($method);

                    $annotationStrings = \de\any\di\ReflectionAnnotation::parseMethodAnnotations($method);
                    $dicMethod->setInject(isset($annotationStrings['inject']));

                    $this->methods[$method->getName()] = $dicMethod;
                }
                
                $this->getCache()->store('reflection|'.$this->getClassname().'|methods', $this->methods);
            }
        }

        return $this->methods;
    }

    public function getSetterMethodsAnnotatedWith($annotation) {

        if(!isset($this->methodsAnnotatedWith[$annotation])) {

            $this->methodsAnnotatedWith[$annotation] = $this->getCache()->fetch('reflection|'.$this->getClassname().'|setterMethods|'.$annotation);

            if($this->methodsAnnotatedWith[$annotation] === false) {
              
                foreach($this->getReflectionClass()->getMethods() as $method) {
                    if($method->isConstructor() || $method->isDestructor() || $method->isStatic())
                        continue;

                    $annotationStrings = \de\any\di\ReflectionAnnotation::parseMethodAnnotations($method);

                    if(!isset($annotationStrings[$annotation]))
                        continue;
                    
                    $dicMethod = new \de\any\di\reflection\method\standard($method->getName());
                    $dicMethod->setParamsByReflectionMethod($method);

                    if(isset($annotationStrings['inject']))
                        $dicMethod->setInject(true);

                    $this->methodsAnnotatedWith[$annotation][$method->getName()] = $dicMethod;
                }

                $this->getCache()->store('reflection|'.$this->getClassname().'|setterMethods|'.$annotation, $this->methodsAnnotatedWith[$annotation]);
            }
        }

        return $this->methodsAnnotatedWith[$annotation];
    }

    public function getInjectProperties() {
            if(!$this->injectProperties) {
                $this->injectProperties = $this->getCache()->fetch('reflection|'.$this->getClassname().'|injProp');

                if($this->injectProperties === false) {
                     foreach($this->getReflectionClass()->getProperties() as $reflectionProperty) {

                        $annotationStrings = \de\any\di\ReflectionAnnotation::parsePropertyAnnotations($reflectionProperty);

                        if(!isset($annotationStrings['var']))
                            continue;

                        if(count($annotationStrings['var']) !== 1) {
                            throw new \de\any\di\exception\parse('multiple @var annotation is not supportet');
                        }

                        if(strpos($annotationStrings['var'][0], '!inject') === false)
                            continue;

                        $classname = \de\any\di\ReflectionAnnotation::parsePropertyVarAnnotation($annotationStrings['var']);

                        $binding = new \de\any\di\binder($classname['class']);
                        $binding->setConcern($classname['concern']);

                        $this->injectProperties[$reflectionProperty->getName()] = $binding;
                     }

                $this->getCache()->store('reflection|'.$this->getClassname().'|injProp', $this->injectProperties);
            }
        }

        return $this->injectProperties;
    }

    public function getClassname() {
        return $this->classname;
    }

    public function setReflectionClass($reflectionClass) {
        $this->reflectionClass = $reflectionClass;
    }

    public function getConstructor() {
        $methods = $this->getMethods();

        return $methods['__construct'];
    }

    /**
     * @return \ReflectionClass
     */
    public function getReflectionClass() {
        if($this->reflectionClass == null) {
            try {
                $this->reflectionClass = new \ReflectionClass($this->getClassname());
            } catch(\ReflectionException $e) {
                throw new \Exception('can\'t find class '.$this->getClassname());
            }
        }

        return $this->reflectionClass;
    }

    public function implementsInterface($interface) {
        return $this->getReflectionClass()->implementsInterface($interface);
    }

    public function setCache($cache) {
        self::$cache = $cache;
    }

    /**
     * @return \de\any\di\iCache
     */
    public function getCache() {
        if(self::$cache === null) {

            if(function_exists('apc_cache_info'))
                self::$cache = new \de\any\di\cache\apc();
            else
                self::$cache = new \de\any\di\cache\memory();
        }

        return self::$cache;
    }

}