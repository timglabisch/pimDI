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

    function testSetIsDecorated() {
        $binder = new binder('$');
        $binder->setIsDecorated(true);
        $this->assertEquals($binder->isDecorated(), true);
        $binder->setIsDecorated(false);
        $this->assertEquals($binder->isDecorated(), false);
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
                ->shared(true)
                ->decorated(true);

        $this->assertEquals($binder->getInterfaceImpl(), 'interface');
        $this->assertEquals($binder->getConcern(), 'concern');
        $this->assertEquals($binder->isShared(), true);
        $this->assertEquals($binder->isDecorated(), true);
    }

    function testDecoratedWith() {
        $binder = new binder('$');
        $binder->to('interface')->decoratedWith('foobar');

        $this->assertEquals($binder->getInterfaceImpl(), 'foobar');
        $this->assertEquals($binder->isDecorated(), true);
    }

    public function testIsFluent() {
        $binder = new binder('$');

        $return = $binder->to('interface')
                ->concern('concern')
                ->shared(true)
                ->decorated(true)
                ->decoratedWith('class')
                ->setArgements('abc')
                ->setConcern('concern')
                ->setInterfaceImpl('interface')
                ->setInterfaceName('interfaceName')
                ->setIsDecorated(true)
                ->setIsShared('true');

        $this->assertTrue($binder === $return);
    }
}