<?php

class decoratorDecorated_base1_decorator implements decoratorDecorated_iBase1, \de\any\di\iDecorateable {

    public function setDecotaredClass($original) {
        $this->parent = $original;
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