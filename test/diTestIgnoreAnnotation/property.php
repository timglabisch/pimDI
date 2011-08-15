<?php
class diTestIgnoreAnnotation_property implements istd {
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