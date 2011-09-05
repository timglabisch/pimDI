<?
namespace de\any\di\cache;

class memory implements \de\any\di\iCache {

    private static $memory = array();

    public function fetch($key) {
        if(!isset(self::$memory[$key]))
            return false;

        return self::$memory[$key];
    }

    public function store($key, $val) {
        self::$memory[$key] = $val;
    }
    
}
