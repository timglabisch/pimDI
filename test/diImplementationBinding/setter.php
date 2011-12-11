<?php
namespace diImmplementationBinding;

require_once __DIR__.'/impl.php';

class setter {

    public $service;

    /**
     * @inject
     **/
    public function foo(impl $impl) {
        $this->service = $impl;
    }

}