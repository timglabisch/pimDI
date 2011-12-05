<?php

namespace diTestIgnoreAnnotation;

class property implements \diTest\istd {
    /** @var \stdClass */
    public $basic;

    /** @author tim glabisch */
    public $author;

    /**
      * @Entity
      * @Table(name="my_persistent_class")
      */
    public $doctrine;
}