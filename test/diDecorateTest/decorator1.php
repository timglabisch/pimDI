<?php
class diDecorateDecorator1 implements istd, \de\any\di\iDecorateable {

    private $original;

    public function setDecotaredClass($original) {
        $this->original = $original;
    }

    public function foo() {
        return $this->original->foo().', decorated1!';
    }

}