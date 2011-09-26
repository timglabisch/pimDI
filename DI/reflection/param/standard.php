<?php
namespace de\any\di\reflection\param;

class standard implements \de\any\di\reflection\iParam  {

    private $concern;
    private $interface;
    private $inject;

    public function setConcern($concern) {
        $this->concern = $concern;
    }

    public function getConcern() {
        return $this->concern;
    }

    public function setInterface($interface) {
        $this->interface = $interface;

        if(substr($interface, 0, 1) != '\\')
            $this->interface = '\\'.$interface;
    }

    public function getInterface() {
        return $this->interface;
    }

    public function setInject($inject) {
        $this->inject = $inject;
        return $this;
    }

    public function getInject() {
        return $this->inject;
    }
}