<?php

namespace de\any\di\test;
use de\any;
use de\any\di\binder;
use de\any\di\binder\repository;

require_once __DIR__ . '/../../../di.php';

class binderRepositoryTest extends \PHPUnit_Framework_TestCase {
    
    function testDecorators() {

        $repository = new repository();

        $binder1 = new binder('istd');
        $binder1->to('std1');
        $repository->addBinding($binder1);

        $binder2 = new binder('istd');
        $binder2->to('std2')->decorated(true);
        $repository->addBinding($binder2);

        $this->assertEquals($repository->getBinding('istd'), $binder1);

        $decorators = $repository->getBindingDecorators('istd');

        $this->assertEquals(count($decorators), 1);

        $this->assertEquals($decorators[0], $binder2);

        return $repository;

    }

    /**
     * @depends testDecorators
     */
    function testMultipleDecorators(repository $repository) {

        $binder1 = new binder('istd');
        $binder1->to('std1')->decorated(true);
        $repository->addBinding($binder1);

        $binder2 = new binder('istd');
        $binder2->to('std2')->decorated(true);
        $repository->addBinding($binder2);

        $decorators = $repository->getBindingDecorators('istd');
        $this->assertEquals(count($decorators), 3);
        $this->assertEquals($decorators[1], $binder1);
        $this->assertEquals($decorators[2], $binder2);
    }

    function testAddBindings() {
        $repository = new repository();

        $binder1 = new binder('istd');
        $binder1->to('std1');

        $binder2 = new binder('istd2');
        $binder2->to('std1');

        $repository->addBindings(array($binder1, $binder2));

        $this->assertTrue($repository->getBinding('istd') === $binder1);
        $this->assertTrue($repository->getBinding('istd2') === $binder2);
    }

    function testOverwritebindings() {
        $repository = new repository();

        $binder1 = new binder('istd');
        $binder1->to('std');
        $binder2 = new binder('istd');
        $binder2->to('std2');

        $repository->addBindings(array($binder1, $binder2));

        $this->assertTrue($repository->getBinding('istd') !== $binder1);
        $this->assertTrue($repository->getBinding('istd') === $binder2);
    }
}