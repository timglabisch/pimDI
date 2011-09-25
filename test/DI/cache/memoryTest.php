<?php
namespace de\any\di\cache\test;

class memoryTest extends \PHPUnit_Framework_TestCase {

    /** \de\any\di\cache\apc */
    public $cache;

    public function setUp() {

        if(!function_exists('apc_cache_info'))
            $this->markTestSkipped();

        $this->cache = new \de\any\di\cache\memory();
    }

    public function testStoreFetch() {
        $this->assertEquals(false, $this->cache->fetch('key1'));

        $this->cache->store('key1', 'value1');
        $this->assertEquals('value1', $this->cache->fetch('key1'));
    }


}
