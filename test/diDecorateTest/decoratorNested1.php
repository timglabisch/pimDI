<?php
class diDecorateDecoratorNested1 implements istd, \de\any\di\iDecorateable  {

    private $original;

    public function setDecotaredClass($original) {
        $this->original = $original;
    }

    /**
     * @inject
     */
    public function setService(nested_inestedservice1 $service) {
        $this->service = $service;
    }

    public function getService() {
        return $this->service;
    }

}