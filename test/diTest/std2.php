<?php
class std2 {

    var $o = null;

    public function foo2() {

    }

    /**
     * @inject iostd
     */
    public function setIoStd(iostd $var) {
        $this->o = $var;
    }

    public function getIoStd() {
        return $this->o;
    }
    
}