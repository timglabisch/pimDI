<?php

class sharedDecorators_base1_decorator implements sharedDecorators_iBase1, \de\any\di\iDecorateable {

    private $parent;

    public function setDecotaredClass($original) {
        $this->parent = $original;
    }

    public function getService() {
        return $this;
    }

}
