<?
namespace de\any\di\cache;

class memApc implements \de\any\di\iCache {

    private static $memory = array();

    public function fetch($key) {
        if(!isset(self::$memory[$key]))
            return apc_fetch($key);

        return self::$memory[$key];
    }

    public function store($key, $val) {
        apc_store($key, $val);
        self::$memory[$key] = $val;
    }

}
