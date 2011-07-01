<?php

class binder {

    private $interfaceName;
    private $interfaceImpl;
    private $named;

    private function verifInterfaceExists($interface) {
        if(!interface_exists('\\'.$interface))
            throw new Exception('interface '.$interface.' does not exists.');
    }

    function __construct($interfaceName) {
        $this->verifInterfaceExists($interfaceName);
        
        $this->interfaceName = $interfaceName;
    }

    function to($interfaceImpl) {
        $this->interfaceImpl = $interfaceImpl;
        return $this;
    }

    function named($named) {
        $this->named = $named;
        return $this;
    }

    public function getHashKey() {
        return $this->getInterfaceName().'|'.$this->getNamed();
    }

    public function setInterfaceImpl($interfaceImpl)
    {
        $this->interfaceImpl = $interfaceImpl;
    }

    public function getInterfaceImpl() {
        return $this->interfaceImpl;
    }

    public function setInterfaceName($interfaceName) {
        $this->interfaceName = $interfaceName;
    }

    public function getInterfaceName() {
        return $this->interfaceName;
    }

    public function setNamed($named) {
        $this->named = $named;
    }

    public function getNamed() {
        return $this->named;
    }
}
