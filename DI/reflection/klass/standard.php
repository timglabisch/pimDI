<?php
namespace de\any\di\reflection\klass;

class standard implements \de\any\di\reflection\iKlass  {

    private $classname;
    private $methods = null;
    private $reflectionClass = null;
    private $properties;

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
        $methods = $this->getMethods();

        return isset($methods[$method]);
    }

    public function getMethods() {
        if($this->methods == null) {
            $this->methods = apc_fetch('reflection|'.$this->getClassname().'|methods');

            if(!$this->methods) {
                $methods = $this->getReflectionClass()->getMethods();

                $this->methods = array();

                foreach($methods as $method) {
                    $this->methods[$method->getName()] = $method;
                }
                
                apc_store('reflection|'.$this->getClassname(), $this->methods.'|methods');
            }
        }

        return $this->methods;
    }

    public function getProperties() {
        if($this->properties == null) {

            $this->properties = apc_fetch('reflection|'.$this->getClassname().'|properties');

            if(!$this->properties) {
                $this->properties = $this->getReflectionClass()->getProperties();

                apc_store('reflection|'.$this->getClassname(), $this->methods.'|properties');
            }
        }

        return $this->properties;
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