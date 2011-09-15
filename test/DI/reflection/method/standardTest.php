<?
namespace de\any\di\reflection\method\test;
use \de\any\di\reflection\method\standard as method;

class standardTest extends \PHPUnit_Framework_TestCase {

    /** @var  \de\any\di\reflection\method\standard */
    var $reflection;


    public function testInvokeArgs0() {
        $mock = parent::getMock('stdClass', array('test'));
        $mock->expects($this->once())
             ->method('test')
             ->with();

        $method = new method('test');
        $method->invokeArgs($mock, array());

    }

    public function testInvokeArgs1() {
        $mock = parent::getMock('stdClass', array('test'));
        $mock->expects($this->once())
             ->method('test')
             ->with($this->equalTo(1));

        $method = new method('test');
        $method->invokeArgs($mock, array(1));

    }

    public function testInvokeArgs2() {
        $mock = parent::getMock('stdClass', array('test'));
        $mock->expects($this->once())
             ->method('test')
             ->with($this->equalTo(1), $this->equalTo(2));

        $method = new method('test');
        $method->invokeArgs($mock, array(1, 2));

    }

     public function testInvokeArgs3() {
        $mock = parent::getMock('stdClass', array('test'));
        $mock->expects($this->once())
             ->method('test')
             ->with($this->equalTo(1), $this->equalTo(2), $this->equalTo(3));

        $method = new method('test');
        $method->invokeArgs($mock, array(1, 2, 3));

    }

    public function testInvokeArgs4() {
        $mock = parent::getMock('stdClass', array('test'));
        $mock->expects($this->once())
             ->method('test')
             ->with($this->equalTo(1), $this->equalTo(2), $this->equalTo(3), $this->equalTo(4));

        $method = new method('test');
        $method->invokeArgs($mock, array(1, 2, 3, 4));

    }

    public function testInvokeArgs5() {
        $mock = parent::getMock('stdClass', array('test'));
        $mock->expects($this->once())
             ->method('test')
             ->with($this->equalTo(1), $this->equalTo(2), $this->equalTo(3), $this->equalTo(4), $this->equalTo(5));

        $method = new method('test');
        $method->invokeArgs($mock, array(1, 2, 3, 4, 5));

    }

    public function testInvokeArgs6() {
        $mock = parent::getMock('stdClass', array('test'));
        $mock->expects($this->once())
             ->method('test')
             ->with($this->equalTo(1), $this->equalTo(2), $this->equalTo(3), $this->equalTo(4), $this->equalTo(5), $this->equalTo(6));

        $method = new method('test');
        $method->invokeArgs($mock, array(1, 2, 3, 4, 5, 6));

    }

    public function testInvokeArgs7() {
        $mock = parent::getMock('stdClass', array('test'));
        $mock->expects($this->once())
             ->method('test')
             ->with($this->equalTo(1), $this->equalTo(2), $this->equalTo(3), $this->equalTo(4), $this->equalTo(5), $this->equalTo(6), $this->equalTo(7));

        $method = new method('test');
        $method->invokeArgs($mock, array(1, 2, 3, 4, 5, 6, 7));

    }



}
