<?php

namespace de\any\di\binder\parser\test;
use de\any\di\binder\parser\xml;

require_once __DIR__.'/../../../../DI/binder/parser/xml.php';

class xmlTest extends \PHPUnit_Framework_TestCase {

    /** @var xml */
    private $xml;

    public function testGetBindings() {
        $this->xml = new xml(file_get_contents(__DIR__.'/../../../fixtures/parse/xml/basic.xml'));
        $bindings = $this->xml->getBindings();

        $this->assertEquals(count($bindings), 1);

        $this->assertEquals($bindings[0]->getInterfaceName(), 'iFoo');
        $this->assertEquals($bindings[0]->getInterfaceImpl(), 'foo');
    }

}