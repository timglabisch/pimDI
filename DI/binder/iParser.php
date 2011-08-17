<?php

namespace de\any\di\binder;
use de\any\di\binder;

interface parser {

    public function parse($str);

    /** @return \de\any\di\binder\repository */
    public function getBindingRepository();

}
