<?php
class nested_objectDouble implements nested_iobject {

    var $service;
    var $service2;

    /**
     * @inject
     */
    public function setNestedService1_double(nested_inestedservice1 $service, nested_inestedservice1 $service2) {
        $this->service = $service;
        $this->service2 = $service2;
    }
    
    public function getNestedService1() {
        return $this->service;
    }

    public function getNestedService1_2() {
        return $this->service2;
    }

}