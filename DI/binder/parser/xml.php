<?php

namespace de\any\di\binder\parser;
use de\any\di\binder\parser;
use de\any\di\binder;

require_once __DIR__.'/../iParser.php';
require_once __DIR__.'/../../binder.php';

class xml {

    /**
     * @var \SimpleXMLElement
     */
    private $simpleXml;

    public function __construct($string) {
        $this->simpleXml = new \SimpleXMLElement($string);
    }


    public function getBindings() {
        $buffer = array();

        if(!count($this->simpleXml))
            return $buffer;

        foreach($this->simpleXml as $v) {

            $binding = new binder($this->simpleXml->bind['interface']->__toString());
            $binding->to($this->simpleXml->bind['to']->__toString());

            if(isset($this->simpleXml->bind['shared']))
                $binding->shared((bool)$this->simpleXml->bind['shared']->__toString());

            if(isset($this->simpleXml->bind['decorated']))
                $binding->setIsDecorated($this->simpleXml->bind['decorated']->__toString());
            
            $buffer[] = $binding;
        }

        return $buffer;
    }

}
