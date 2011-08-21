<?php

namespace de\any\di\test;
use de\any\di;
use de\any\di\binder;

require_once __DIR__ . '/../di.php';

array_map(function($v) { include_once  $v; }, glob(__DIR__.'/'.basename(__FILE__,'.php').'/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diNestedTest/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diConstructor/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diDecorateTest/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diDecoratorNeedDecorated/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diSharedDecorators/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diParam/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diCircular/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diCircularNested/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diPropertyParseException/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diTestIgnoreAnnotation/*.php'));

class DITest extends \PHPUnit_Framework_TestCase {

    public function testDiSet() {
        $di = new di();
        $di->bind('istd')->to('std1');

        $this->assertInstanceOf('std1', $di->get('istd'));
        return $di;
    }

    /**
     * @depends testDiSet
     */
    public function testDiOverwrite($di) {
        $di->bind('istd')->to('std2');

        $this->assertInstanceOf('std2', $di->get('istd'));
    }

    public function testDIConcern() {
        $di = new di();
        $di->bind('istd')->to('std1');
        $di->bind('istd')->to('std2')->concern('abc');

        $this->assertInstanceOf('std2', $di->get('istd', 'abc'));#
        $this->assertInstanceOf('std1', $di->get('istd'));
    }

    /**
     * @expectedException Exception
     */
    public function testInterfaceDoesNotExists() {
        $di = new di();
        $di->bind('UNKNOWN')->to('std1');

        $this->assertInstanceOf('std1', $di->get('UNKNOWN'));
    }

    public function testBasicInjection() {
       $di = new di();
       $di->bind('nested_iobject')->to('nested_object');
       $di->bind('nested_inestedservice1')->to('nested_nestedservice1');

       $this->assertInstanceOf('nested_inestedservice1', $di->get('nested_iobject')->getNestedService1());
    }

     public function testDoubleInjection() {
       $di = new di();
       $di->bind('nested_iobject')->to('nested_objectDouble');
       $di->bind('nested_inestedservice1')->to('nested_nestedservice1');

       $this->assertInstanceOf('nested_inestedservice1', $di->get('nested_iobject')->getNestedService1());
       $this->assertInstanceOf('nested_inestedservice1', $di->get('nested_iobject')->getNestedService1_2());
    }

     public function testDouble2Injection() {
       $di = new di();
       $di->bind('nested_iobject')->to('nested_objectDouble2');
       $di->bind('nested_inestedservice1')->to('nested_nestedservice1');

       $this->assertInstanceOf('nested_inestedservice1', $di->get('nested_iobject')->getNestedService1());
       $this->assertInstanceOf('nested_inestedservice1', $di->get('nested_iobject')->getNestedService1_2());
    }

   public function testConcern() {
       $di = new di();
       $di->bind('nested_iobject')->to('nested_objectConcern');
       $di->bind('nested_inestedservice1')->to('nested_nestedservice1');
       $di->bind('nested_inestedservice1')->to('nested_nestedservice2')->concern('abc');

       $this->assertInstanceOf('nested_nestedservice1', $di->get('nested_iobject')->getNestedService1());
       $this->assertInstanceOf('nested_nestedservice2', $di->get('nested_iobject')->getNestedService1_2());
    }

    public function testSharedDefault() {
       $di = new di();
       $di->bind('istd')->to('std1');

       $this->assertTrue($di->get('istd') !== $di->get('istd'));
    }

    public function testIsShared() {
       $di = new di();
       $di->bind('istd')->to('std1')->shared(true);

       $this->assertTrue($di->get('istd') === $di->get('istd'));
    }

    function testConstructorInjection() {
        $di = new di();
        $di->bind('constructor_istd')->to('constructor_std1');
        $di->bind('constructor_istd')->to('constructor_std2')->concern('std2');
        $instance = $di->get('constructor_istd');

        $this->assertInstanceOf('constructor_std2', $instance->getService());
    }

    function testCreateInstanceFromClassname() {
        $di = new di();
        $di->bind('constructor_istd')->to('constructor_std1');
        $di->bind('constructor_istd')->to('constructor_std2')->concern('std2');
        $instance = $di->createInstanceFromClassname('constructor_std1');

        $this->assertInstanceOf('constructor_std2', $instance->getService());
    }

    public function testDecorate() {
        $di = new di();
        $di->bind('istd')->to('diDecorateStd1');
        $di->bind('istd')->to('diDecorateDecorator1')->decorated(true);

        $this->assertEquals($di->get('istd')->foo(), 'foo, decorated1!');
    }

    public function testDecorateMultiple() {
        $di = new di();
        $di->bind('istd')->to('diDecorateStd1');
        $di->bind('istd')->to('diDecorateDecorator1')->decorated(true);
        $di->bind('istd')->to('diDecorateDecorator2')->decorated(true);

        $this->assertEquals($di->get('istd')->foo(), 'foo, decorated1!, decorated2!');
    }

    public function testDecoratedNested() {
        $di = new di();
        $di->bind('istd')->to('diDecorateStd1');
        $di->bind('istd')->to('diDecorateDecorator1')->decorated(true);
        $di->bind('istd')->to('diDecorateDecoratorNested1')->decorated(true);
        $di->bind('nested_inestedservice1')->to('nested_nestedservice1');

        $this->assertInstanceOf('nested_inestedservice1', $di->get('istd')->getService());
        $this->assertEquals($di->get('istd')->getService()->identify(), 'nested_nestedservice1');
    }

    public function testDecoratedDecorator() {
        $di = new di();
        $di->bind('decoratorDecorated_iBase1')->to('decoratorDecorated_base1');
        $di->bind('decoratorDecorated_iBase1')->to('decoratorDecorated_base1_decorator')->decorated(true);
        $di->bind('decoratorDecorated_iBase2')->to('decoratorDecorated_base2');
        $di->bind('decoratorDecorated_iBase2')->decoratedWith('decoratorDecorated_base2_decorator');

        $this->assertEquals($di->get('decoratorDecorated_iBase1')->getClassname(), 'decoratorDecorated_base1|decoratorDecorated_base1_decorator|decoratorDecorated_base2|decoratorDecorated_base2_decorator');
    }

    function testSharedNotDecorator() {
        $di = new di();
        $di->bind('sharedDecorators_iBase1')->to('sharedDecorators_base1');
        $di->bind('sharedDecorators_iBase1')->decoratedWith('sharedDecorators_base1_decorator');
        $decorator = $di->get('sharedDecorators_iBase1')->getService();

        $this->assertInstanceOf('sharedDecorators_base1_decorator', $decorator);
        $this->assertTrue($di->get('sharedDecorators_iBase1')->getService() !== $decorator);
    }

    function testSharedDecorator() {
        $di = new di();
        $di->bind('sharedDecorators_iBase1')->to('sharedDecorators_base1');
        $di->bind('sharedDecorators_iBase1')->decoratedWith('sharedDecorators_base1_decorator')->shared(true);
        $decorator = $di->get('sharedDecorators_iBase1')->getService();

        $this->assertInstanceOf('sharedDecorators_base1_decorator', $decorator);
        $this->assertTrue($di->get('sharedDecorators_iBase1')->getService() === $decorator);
    }

    public function testSetBinderRepositoy() {
        $di = new di();
        $class = new \stdClass();
        $di->setBinderRepository($class);

        $this->assertTrue($class === $di->getBinderRepository());
    }

    public function testDefaultBinderRepository() {
        $di = new di();
        
        $this->assertInstanceOf('\de\any\di\binder\repository', $di->getBinderRepository());
    }

    public function testBindInstances() {
        $di = new di();
        $std = new \std1();
        $di->bind('istd')->to($std);

        $this->assertTrue($di->get('istd') === $std);
        return $di;
    }

    /**
     * @depends testBindInstances
     */
    public function testBindSharedInstances($di) {
        $std = new \std1();
        $di->bind('istd')->to($std)->shared(false);

        $this->assertTrue($di->get('istd') !== $std);
        $this->assertInstanceOf('\std1', $di->get('istd'));
    }

    public function testInjectDiItself() {
        $di = new di();
        $di->bind('\de\any\iDi')->to($di);

        $this->assertTrue($di === $di->get('\de\any\iDi'));
    }

    public function testParam() {
        $di = new di();
        $di->bind('istd')->to('diParam_standard');
        $di->bind('iostd')->to('diParam_standard_injected');

        $this->assertInstanceOf('diParam_standard', $di->get('istd'));
        $this->assertInstanceOf('diParam_standard_injected', $di->get('istd')->service);
        return $di;
    }


    public function testClosure() {
        $di = new di();
        $di->foo = function() { return 'abc'; };

#        var_dump(($di->foo)());
##        $this->assertEquals('abc', $di->foo());
    }

    public function testParamConcern() {
        $di = new di();
        $di->bind('istd')->to('diParam_concern');
        $di->bind('iostd')->to('diParam_standard_injected');
        $di->bind('iostd')->to('ostd1')->concern('abc');

        $this->assertInstanceOf('diParam_concern', $di->get('istd'));
        $this->assertInstanceOf('diParam_standard_injected', $di->get('istd')->service);
        $this->assertInstanceOf('ostd1', $di->get('istd')->service_concern);
        return $di;
    }

    /**
       * A Depends on B and B depends on A,
       * @expectedException \de\any\di\exception\circular
       */
    public function testCicular() {
        $di = new di();
        $di->bind('iCircular')->to('circular_a')->concern('a');
        $di->bind('iCircular')->to('circular_b')->concern('b');

        $this->assertInstanceOf('circular_a', $di->get('iCircular', 'a'));
    }

   /**
     * B Depends on C and C depends on A, get instance A
     * @expectedException \de\any\di\exception\circular
     */
    public function testCicularNested() {
        $di = new di();
        $di->bind('iCircular')->to('circularNested_a')->concern('a');
        $di->bind('iCircular')->to('circularNested_b')->concern('b');
        $di->bind('iCircular')->to('circularNested_c')->concern('c');

        $this->assertInstanceOf('circular_a', $di->get('iCircular', 'a'));
    }

   /**
     * @expectedException  \de\any\di\exception\parse
     */
    public function testPropertyParseException() {
        $di= new di();
        $di->bind('istd')->to('diPropertyParseException_std1');
        $di->get('istd');
    }

    public function testIgnoreAnnotationProperty() {
        $di = new di();
        $di->bind('istd')->to('diTestIgnoreAnnotation_property');
        $this->assertNull($di->get('istd')->basic);
        $this->assertNull($di->get('istd')->author);
        $this->assertNull($di->get('istd')->doctrine);
    }

    public function testIgnoreAnnotationMethod() {
        $di = new di();
        $di->bind('istd')->to('diTestIgnoreAnnotation_method');
        $this->assertTrue($di->get('istd')->basic());
        $this->assertTrue($di->get('istd')->author());
        $this->assertTrue($di->get('istd')->doctrine());
    }

}