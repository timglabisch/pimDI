<?php
namespace diImmplementationBinding;

require_once __DIR__.'/impl.php';

class constructor {

    public $service;

    /**
     * @inject
     **/
    public function __construct(impl $impl) {
        $this->service = $impl;
    }

}