<?php
namespace de\any\di;

require_once __DIR__.'/cache/file.php';
require_once __DIR__.'/cache/apc.php';

interface iCache {
    public function fetch($key);
    public function store($key, $value);
}