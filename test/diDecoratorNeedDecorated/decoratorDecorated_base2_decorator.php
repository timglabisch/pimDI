<?php

class decoratorDecorated_base2_decorator implements decoratorDecorated_iBase2 {

    public function __construct($parent) {
        $this->parent = $parent;
    }

    function getClassname() {
        return $this->parent->getClassname().'|'.__CLASS__;
    }

}