<?php

class binder {

    private $interfaceName;
    private $interfaceImpl;
    private $concern;
    private $shared = false;
    private $argements = array();

    function __construct($interfaceName) {
        $this->interfaceName = $interfaceName;
    }

    function to($interfaceImpl) {
        $this->interfaceImpl = $interfaceImpl;
        return $this;
    }

    function concern($named) {
        $this->setConcern($named);
        return $this;
    }

    public function getHashKey() {
        return $this->getInterfaceName().'|'.$this->getConcern();
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

    public function setConcern($named) {
        $this->concern = $named;
        return $this;
    }

    public function getConcern() {
        return $this->concern;
    }

    public function setArgements($argements)
    {
        $this->argements = $argements;
    }

    public function getArgements()
    {
        return $this->argements;
    }

    public function setShared($shared)
    {
        $this->shared = (bool)$shared;
        return $this;
    }

    public function shared($shared) {
        return $this->setShared($shared);
    }

    public function getShared()
    {
        return $this->shared;
    }
}
