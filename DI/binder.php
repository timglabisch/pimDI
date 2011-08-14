<?php

namespace de\any\di;

require_once __DIR__ . '/binder/repository.php';

class binder {

    private $interfaceName;
    private $interfaceImpl;
    private $concern;
    private $shared = false;
    private $arguments = array();
    private $decorated = false;
    private $instance;

    function __construct($interfaceName) {
        $this->interfaceName = $interfaceName;
    }

    private function toObject($obj) {
        $this->setInterfaceImpl(get_class($obj));
        $this->setInstance($obj);
        $this->setIsShared(true);
        return $this;
    }

    function to($interfaceImpl) {

        if(is_object($interfaceImpl))
            return $this->toObject($interfaceImpl);

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
        return $this;
    }

    public function getInterfaceImpl() {
        return $this->interfaceImpl;
    }

    public function setInterfaceName($interfaceName) {
        $this->interfaceName = $interfaceName;
        return $this;
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

    public function setArguments($argements)
    {
        $this->arguments = $argements;
        return $this;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function setIsShared($shared)
    {
        $this->shared = (bool)$shared;
        return $this;
    }

    public function shared($shared) {
        return $this->setIsShared($shared);
    }

    public function isShared()
    {
        return $this->shared;
    }

    public function decorated($decorated) {
        return $this->setIsDecorated($decorated);
    }

    public function setIsDecorated($decorate)
    {
        $this->decorated = (bool)$decorate;
        return $this;
    }

    public function isDecorated()
    {
        return $this->decorated;
    }

    public function decoratedWith($class) {
        $this->setIsDecorated(true);
        $this->setInterfaceImpl($class);
        return $this;
    }

    public function setInstance($instance)
    {
        $this->instance = $instance;
        return $this;
    }

    public function getInstance()
    {
        return $this->instance;
    }

}