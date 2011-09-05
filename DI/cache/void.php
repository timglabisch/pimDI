<?
namespace de\any\di\cache;

class void implements \de\any\di\iCache {

    public function fetch($key) {
        return false;
    }

    public function store($key, $val) {
        
    }
    
}
