<?php

namespace de\any\di\exception;
use \de\any\di\exception;

class circular extends exception {

    function __construct($a, $b) {
        $this->message = $a.' depends on '.$b.' and '.$b.' on '.$a;
    }

}


