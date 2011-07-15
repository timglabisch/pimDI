<?php
class diDecorateDecoratorNested1 implements istd {

    private $original;

    public function __construct(istd $original) {
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