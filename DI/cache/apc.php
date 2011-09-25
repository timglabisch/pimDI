<?
namespace de\any\di\cache;

class apc implements \de\any\di\iCache {

    public function fetch($key) {
        return apc_fetch($key);
    }

    public function store($key, $val) {
        apc_add($key, $val);
    }

}
