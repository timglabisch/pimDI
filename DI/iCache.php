<?php
namespace de\any\di;

require_once __DIR__.'/cache/apc.php';
require_once __DIR__.'/cache/void.php';
require_once __DIR__.'/cache/memory.php';

interface iCache {
    public function fetch($key);
    public function store($key, $value);
}