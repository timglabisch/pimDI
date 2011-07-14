<?php

class decoratorDecorated_base1_decorator implements decoratorDecorated_iBase1 {

    public function __construct($parent) {
        $this->parent = $parent;
    }

    /**
     * @inject
     */
    public function setDecoratorDecorated_iBase2(decoratorDecorated_iBase2 $decoratorDecorated_iBase2) {
        $this->decoratorDecorated_iBase2 = $decoratorDecorated_iBase2;
    }

    function getClassname() {
        return $this->parent->getClassname().'|'.__CLASS__.'|'.$this->decoratorDecorated_iBase2->getClassname();
    }

}