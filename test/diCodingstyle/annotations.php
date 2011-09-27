<?php

namespace test\codingstyle;

class annotations implements \constructor_istd {

    /** @var \test\diCodingstyle\iInjected_in_namespace !inject */
    public $i1;


    // doesn't work! that's right
    /* @var test\diCodingstyle\iInjected_in_namespace !inject */
    public $i2;
   
    public $i3;
    public $i4;

    /**
       * @inject
       */
    public function injectI3(\test\diCodingstyle\iInjected_in_namespace $i3) {
        $this->i3 = $i3;
    }

    // doesn't work! that's right
    /*
       * @inject
       */
    public function injectI4(\test\diCodingstyle\iInjected_in_namespace $i4) {
        $this->i4 = $i4;
    }

}