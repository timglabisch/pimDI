<?php

namespace de\any\di\test;
use de\any;
use de\any\di\binder;

require_once __DIR__.'/../../DI.php';

class binderTest extends \PHPUnit_Framework_TestCase {
    
    function testConstruct() {
        $binder = new binder('interface');
        $this->assertEquals($binder->getInterfaceName(), 'interface');
    }

    function testSetConcern() {
        $binder = new binder('$');
        $binder->setConcern('conc');
        $this->assertEquals($binder->getConcern(), 'conc');
    }

    function testSetInterfaceImpl() {
        $binder = new binder('$');
        $binder->setInterfaceImpl('impl');
        $this->assertEquals($binder->getInterfaceImpl(), 'impl');
    }

    function testSetInterfaceName() {
        $binder = new binder('$');
        $binder->setInterfaceName('impl');
        $this->assertEquals($binder->getInterfaceName(), 'impl');
    }

    function testGetGetHasKey() {
        $binder = new binder('$');
        $this->assertEquals($binder->getHashKey(), '$|');

        $binder->setConcern('conc');
        $this->assertEquals($binder->getHashKey(), '$|conc');
    }

    function testSetGet() {
        $binder = new binder('$');
        $binder->to('interface')
                ->concern('concern')
                ->shared(true);

        $this->assertEquals($binder->getInterfaceImpl(), 'interface');
        $this->assertEquals($binder->getConcern(), 'concern');
        $this->assertEquals($binder->isShared(), 'true');
    }
}