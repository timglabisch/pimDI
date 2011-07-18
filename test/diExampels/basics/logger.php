<?php
namespace de\any\di\test\example\basics;

class logger implements iLogger {

    private $log = '';

    function log($what) {
        $this->log .= $what."\n";
    }

    function getLog() {
        return $this->log;
    }
}