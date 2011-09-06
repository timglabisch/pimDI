<?php

namespace de\any\di\reflection;

interface iKlass {
    public function __construct($class);
    public function hasMethod($method);
    public function getMethods();
    public function getConstructor();
    public function implementsInterface($interface);
    public function getSetterMethodsAnnotatedWith($annotation);
    public function getInjectProperties();
}
