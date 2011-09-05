<?
namespace de\any\di\cache;

class file implements \de\any\di\iCache {

    public function fetch($key) {
        $filename = sys_get_temp_dir().DIRECTORY_SEPARATOR.'anyDi_'.md5($key);

        if(!file_exists($filename))
            return false;

        if(!is_readable($filename))
            return false;

        return unserialize(file_get_contents($filename));
    }

    public function store($key, $val) {
        $filename = sys_get_temp_dir().DIRECTORY_SEPARATOR.'anyDi_'.md5($key);

        return file_put_contents($filename, serialize($val));
    }
    
}
