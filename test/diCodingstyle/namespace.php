<?php
class constructor_namespace implements constructor_istd {

    /** @var \test\diCodingstyle\iInjected_in_namespace !inject */
    public $i1;

    /** @var test\diCodingstyle\iInjected_in_namespace !inject */
    public $i2;

    public $i3;
    public $i4;

    /**
       * @inject
       */
    public function injectI3(test\diCodingstyle\iInjected_in_namespace $i3) {
        $this->i3 = $i3;
    }

     /**
       * @inject
       */
    public function __construct(test\diCodingstyle\iInjected_in_namespace $i4) {
        $this->i4 = $i4;
    }

}