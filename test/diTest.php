<?php

require_once __DIR__.'/../DI.php';

array_map(function($v) { include_once  $v; }, glob(__DIR__.'/'.basename(__FILE__,'.php').'/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diNestedTest/*.php'));

class DITest extends PHPUnit_Framework_TestCase {

    public function testDiSet() {
        $di = new di();

        $di->bind('istd')->to('std1');
        $this->assertInstanceOf('std1', $di->get('istd'));

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

}
 
