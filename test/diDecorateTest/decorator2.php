<?php
class diDecorateDecorator2 implements istd {

    private $original;

    public function __construct(istd $original) {
        $this->original = $original;
    }

    public function foo() {
        return $this->original->foo().', decorated2!';
    }

}