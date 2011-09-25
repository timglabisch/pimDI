<?php
namespace de\any\di\cache\test;

class apcTest extends \PHPUnit_Framework_TestCase {

    /** \de\any\di\cache\apc */
    public $cache;

    public function setUp() {

        if(!function_exists('apc_cache_info'))
            $this->markTestSkipped();

        $this->cache = new \de\any\di\cache\apc();
    }

    public function testStoreFetch() {

        if(!ini_get('apc.enable_cli'))
            $this->markTestSkipped();

        $this->cache->store('key1', 'value1');
        $this->assertEquals('value1', $this->cache->fetch('key1'));
    }


}
