<?php

namespace diTestIgnoreAnnotation;

class method implements \diTest\istd {
    /** @var \stdClass */
    public function basic() {
        return true;
    }

    /** @author tim glabisch */
    public function author() {
        return true;
    }

    /**
      * @Entity
      * @Table(name="my_persistent_class")
      */
    public function doctrine() {
        return true;
    }
}