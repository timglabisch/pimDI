<?php
class diDecorateDecorator2 implements \diTest\istd, \de\any\di\iDecorateable {

    private $original;

    public function setDecotaredClass($original) {
        $this->original = $original;
    }

    public function foo() {
        return $this->original->foo().', decorated2!';
    }

}