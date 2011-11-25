<?
namespace de\any\di\reflection\klass\test;
use \de\any\di\reflection\klass\standard as reflection;

array_map(function($v) { include_once  $v; }, glob(__DIR__.'/standard/*.php'));

class standardTest extends \PHPUnit_Framework_TestCase {

    /** @var  \de\any\di\reflection\klass\standard */
    var $reflection;

    public function testNewInstanceArgs0() {
        $reflection = new reflection('de\any\di\reflection\klass\standard\test\params');
        $instance = $reflection->newInstanceArgs();

        $this->assertEquals($instance->_1, false);
        $this->assertEquals($instance->_2, false);
        $this->assertEquals($instance->_3, false);
        $this->assertEquals($instance->_4, false);
        $this->assertEquals($instance->_5, false);
        $this->assertEquals($instance->_6, false);
        $this->assertEquals($instance->_7, false);
    }

    public function testNewInstanceArgs1() {
        $reflection = new reflection('de\any\di\reflection\klass\standard\test\params');
        $instance = $reflection->newInstanceArgs(array(1));

        $this->assertEquals($instance->_1, 1);
        $this->assertEquals($instance->_2, false);
        $this->assertEquals($instance->_3, false);
        $this->assertEquals($instance->_4, false);
        $this->assertEquals($instance->_5, false);
        $this->assertEquals($instance->_6, false);
        $this->assertEquals($instance->_7, false);
    }

    public function testNewInstanceArgs2() {
        $reflection = new reflection('de\any\di\reflection\klass\standard\test\params');
        $instance = $reflection->newInstanceArgs(array(1, 2));

        $this->assertEquals($instance->_1, 1);
        $this->assertEquals($instance->_2, 2);
        $this->assertEquals($instance->_3, false);
        $this->assertEquals($instance->_4, false);
        $this->assertEquals($instance->_5, false);
        $this->assertEquals($instance->_6, false);
        $this->assertEquals($instance->_7, false);
    }

    public function testNewInstanceArgs3() {
        $reflection = new reflection('de\any\di\reflection\klass\standard\test\params');
        $instance = $reflection->newInstanceArgs(array(1, 2, 3));

        $this->assertEquals($instance->_1, 1);
        $this->assertEquals($instance->_2, 2);
        $this->assertEquals($instance->_3, 3);
        $this->assertEquals($instance->_4, false);
        $this->assertEquals($instance->_5, false);
        $this->assertEquals($instance->_6, false);
        $this->assertEquals($instance->_7, false);
    }

    public function testNewInstanceArgs4() {
        $reflection = new reflection('de\any\di\reflection\klass\standard\test\params');
        $instance = $reflection->newInstanceArgs(array(1, 2, 3, 4));

        $this->assertEquals($instance->_1, 1);
        $this->assertEquals($instance->_2, 2);
        $this->assertEquals($instance->_3, 3);
        $this->assertEquals($instance->_4, 4);
        $this->assertEquals($instance->_5, false);
        $this->assertEquals($instance->_6, false);
        $this->assertEquals($instance->_7, false);
    }

    public function testNewInstanceArgs5() {
        $reflection = new reflection('de\any\di\reflection\klass\standard\test\params');
        $instance = $reflection->newInstanceArgs(array(1, 2, 3, 4, 5));

        $this->assertEquals($instance->_1, 1);
        $this->assertEquals($instance->_2, 2);
        $this->assertEquals($instance->_3, 3);
        $this->assertEquals($instance->_4, 4);
        $this->assertEquals($instance->_5, 5);
        $this->assertEquals($instance->_6, false);
        $this->assertEquals($instance->_7, false);
    }

    public function testNewInstanceArgs6() {
        $reflection = new reflection('de\any\di\reflection\klass\standard\test\params');
        $instance = $reflection->newInstanceArgs(array(1, 2, 3, 4, 5, 6));

        $this->assertEquals($instance->_1, 1);
        $this->assertEquals($instance->_2, 2);
        $this->assertEquals($instance->_3, 3);
        $this->assertEquals($instance->_4, 4);
        $this->assertEquals($instance->_5, 5);
        $this->assertEquals($instance->_6, 6);
        $this->assertEquals($instance->_7, false);
    }

    public function testNewInstanceArgs7() {
        $reflection = new reflection('de\any\di\reflection\klass\standard\test\params');
        $instance = $reflection->newInstanceArgs(array(1, 2, 3, 4, 5, 6, 7));

        $this->assertEquals($instance->_1, 1);
        $this->assertEquals($instance->_2, 2);
        $this->assertEquals($instance->_3, 3);
        $this->assertEquals($instance->_4, 4);
        $this->assertEquals($instance->_5, 5);
        $this->assertEquals($instance->_6, 6);
        $this->assertEquals($instance->_7, 7);
    }

    public function testSetReflectionClass() {
        $reflection = new reflection('\stdClass');
        $class = new \stdClass();
        $reflection->setReflectionClass($class);

        $this->assertTrue($class === $reflection->getReflectionClass());
    }

     public function testSetCache() {
		 $this->markTestSkipped();
        $reflection = new reflection('\stdClass');
        $class = new \stdClass();
        $reflection->setCache($class);

        $this->assertTrue($class === $reflection->getCache());
    }

}
