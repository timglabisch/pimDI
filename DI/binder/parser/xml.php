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
            $binding = new binder($v['interface']->__toString());
            $binding->to($v['to']->__toString());

            if(isset($v['shared']))
                if($v['shared']->__toString() == "true")
                    $binding->shared(true);
                else
                    $binding->shared(false);

            if(isset($v['decorated']))
                if($v['decorated']->__toString() == "true")
                    $binding->setIsDecorated(true);
                else
                    $binding->setIsDecorated(false);

             if(isset($v['concern']))
                    $binding->setConcern($v['concern']->__toString());
            
            $buffer[] = $binding;
        }

        return $buffer;
    }

}
