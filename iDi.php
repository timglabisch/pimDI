<?php
namespace de\any;

interface iDi {
    public function get($service, $concern='', $args=array());
    public function bind($interfaceName);
}
