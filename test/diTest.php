<?php

require_once __DIR__.'/../DI.php';

array_map(function($v) { include_once  $v; }, glob(__DIR__.'/'.basename(__FILE__,'.php').'/*.php'));

class DITest extends PHPUnit_Framework_TestCase {

    public function testDi() {
        $di = new di();
        $di->istd = 'std1';

        $this->assertInstanceOf('std1', $di->istd);

        
    }

    public function testDiSet() {
        $di = new di();
        $di->__set('istd','std1');

        $this->assertInstanceOf('std1', $di->__get('istd'));

        $di->__set('istd','std2');
        $di->iostd = 'ostd1';
        $this->assertInstanceOf('std2', $di->__get('istd'));

        $this->assertInstanceOf('ostd1', $di->__get('istd')->getIoStd());

    }

}
 
