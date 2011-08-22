<?php

namespace de\any\di\exception;
use \de\any\di\exception;

class classNotFound extends exception {
    
    function __construct($class) {
        $this->message = 'class '.$class.' not found';
    }
    
}