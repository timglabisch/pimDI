<?php

class sharedDecorators_base1_decorator implements sharedDecorators_iBase1 {

    private $parent;

    public function __construct(sharedDecorators_iBase1 $parent) {
        $this->parent = $parent;
    }

    public function getService() {
        return $this;
    }

}
