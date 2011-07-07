<?php
class nested_object implements nested_iobject {

    var $service;
    var $service2;
    var $service3;
    /**
     * @inject
     */
    public function setNestedService1(nested_inestedservice1 $service) {
        $this->service = $service;
    }
    
    public function getNestedService1() {
        return $this->service;
    }


}