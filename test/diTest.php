<?php

require_once __DIR__.'/../DI.php';

array_map(function($v) { include_once  $v; }, glob(__DIR__.'/'.basename(__FILE__,'.php').'/*.php'));

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

}
 
