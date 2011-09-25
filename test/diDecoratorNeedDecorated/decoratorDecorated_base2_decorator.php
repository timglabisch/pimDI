<?php

class decoratorDecorated_base2_decorator implements decoratorDecorated_iBase2, \de\any\di\iDecorateable {

    public function setDecotaredClass($original) {
        $this->parent = $original;
    }

    function getClassname() {
        return $this->parent->getClassname().'|'.__CLASS__;
    }

}