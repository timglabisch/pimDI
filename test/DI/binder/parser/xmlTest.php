<?php

namespace de\any\di\binder\parser\test;
use de\any\di\binder\parser\xml;

require_once __DIR__.'/../../../../DI/binder/parser/xml.php';

class xmlTest extends \PHPUnit_Framework_TestCase {

    /** @var \de\any\di\binder\parser\xml */
    private $xml;

    public function testGetBindings() {
        $this->xml = new xml(file_get_contents(__DIR__.'/../../../fixtures/parse/xml/basic.xml'));
        $bindings = $this->xml->getBindings();

        $this->assertEquals(count($bindings), 1);

        $this->assertEquals($bindings[0]->getInterfaceName(), '\iFoo');
        $this->assertEquals($bindings[0]->getInterfaceImpl(), 'foo');
        $this->assertEquals($bindings[0]->isShared(), true);
        $this->assertEquals($bindings[0]->isDecorated(), true);
    }

    public function testGetMultipleBindings() {
        $this->xml = new xml(file_get_contents(__DIR__.'/../../../fixtures/parse/xml/multiple.xml'));
        $bindings = $this->xml->getBindings();

        $this->assertEquals(count($bindings), 3);

        $this->assertEquals($bindings[0]->getInterfaceName(), '\iFoo');
        $this->assertEquals($bindings[0]->getInterfaceImpl(), 'foo');
        $this->assertEquals($bindings[0]->isShared(), true);
        $this->assertEquals($bindings[0]->isDecorated(), false);

        $this->assertEquals($bindings[1]->getInterfaceName(), '\iFoo2');
        $this->assertEquals($bindings[1]->getInterfaceImpl(), 'foo2');
        $this->assertEquals($bindings[1]->isShared(), false);
        $this->assertEquals($bindings[1]->isDecorated(), true);

        $this->assertEquals($bindings[2]->getInterfaceName(), '\iFoo3');
        $this->assertEquals($bindings[2]->getInterfaceImpl(), 'foo3');
        $this->assertEquals($bindings[2]->isShared(), true);
        $this->assertEquals($bindings[2]->isDecorated(), false);
    }

     public function testGetMultipleBindingsConcern() {
        $this->xml = new xml(file_get_contents(__DIR__.'/../../../fixtures/parse/xml/concern.xml'));
        $bindings = $this->xml->getBindings();

        $this->assertEquals(count($bindings), 3);
        $this->assertEquals($bindings[0]->getConcern(), 'a');
        $this->assertEquals($bindings[1]->getConcern(), 'b');
        $this->assertEquals($bindings[2]->getConcern(), 'c');
    }

}