<?php

namespace de\any\di\test\example\basics;
use de\any\di;

require_once __DIR__.'/iLogger.php';
require_once __DIR__.'/logger.php';

class BasicTest extends \PHPUnit_Framework_TestCase {

    function testGetInstance() {
        $di = new di();
        $di->bind('\de\any\di\test\example\basics\iLogger')->to('\de\any\di\test\example\basics\Logger');

        $this->assertInstanceOf('\de\any\di\test\example\basics\Logger', $di->get('\de\any\di\test\example\basics\iLogger'));
        return $di;
    }

    /**
     * @depends testGetInstance
     */
    function testUseInstance($di) {
        $logger = $di->get('\de\any\di\test\example\basics\iLogger');
        $logger->log('hallo');
        $logger->log('welt');

        $this->assertEquals($logger->getLog(), 'hallo'."\n".'welt'."\n");
    }
    
}
