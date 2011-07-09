<?php
class constructor_std1 implements constructor_istd {

    private $service;
    /**
     * @inject std2
     */
    public function __construct(constructor_istd $service) {
        $this->service = $service;
    }

    public function getService() {
        return $this->service;
    }

}