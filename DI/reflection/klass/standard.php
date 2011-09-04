<?php
namespace de\any\di\reflection\klass;

class standard implements \de\any\di\reflection\iKlass  {

    private $classname;
    private $methods = null;
    private $reflectionClass = null;

    public function __construct($classname) {
        $this->setClassname($classname);
    }

    public function newInstance() {
        $classname = $this->getClassname();
        return new $classname;
    }

    public function newInstanceArgs($args) {
        return $this->getReflectionClass()->newInstanceArgs($args);
    }

    public function getName() {
        return $this->classname;
    }

    public function setClassname($classname) {
        $this->classname = $classname;
    }

    public function hasMethod($method) {
        $methods = $this->getMethodNames();

        return isset($methods[$method]);
    }

    private function getMethodNames() {
        if($this->methods == null) {
            $this->methods = apc_fetch('reflection|'.$this->getClassname());

            if(!$this->methods) {
                $methods = $this->getReflectionClass()->getMethods();

                $this->methods = array();

                foreach($methods as $method) {
                    $this->methods[$method->getName()] = $method->getName();
                }

                apc_store('reflection|'.$this->getClassname(), $this->methods);
            }
        }

        return $this->methods;
    }

    public function getProperties() {
        return $this->getReflectionClass()->getProperties();
    }

    public function getMethods() {
        $buffer = array();
        foreach($this->getMethodNames() as $method)
            $buffer[] = new \ReflectionMethod($this->getClassname(), $method);

        return $buffer;
    }

    public function getClassname() {
        return $this->classname;
    }

    public function setReflectionClass($reflectionClass) {
        $this->reflectionClass = $reflectionClass;
    }

    public function getConstructor() {
        return $this->getReflectionClass()->getConstructor();
    }

    /**
     * @return \ReflectionClass
     */
    public function getReflectionClass() {
        if($this->reflectionClass == null)
            $this->reflectionClass = new \ReflectionClass($this->getClassname());

        return $this->reflectionClass;
    }

    public function implementsInterface($interface) {
        return $this->getReflectionClass()->implementsInterface($interface);
    }

}