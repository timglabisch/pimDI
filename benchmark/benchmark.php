<?php

require_once __DIR__.'/../di.php';

array_map(function($v) { include_once  $v; }, glob(__DIR__.'/../test/diTest/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/../test/diNestedTest/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/../test/diConstructor/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/../test/diDecorateTest/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/../test/diDecoratorNeedDecorated/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/../test/diSharedDecorators/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/../test/diParam/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/../test/diCircular/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/../test/diCircularNested/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/../test/diPropertyParseException/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/../test/diTestIgnoreAnnotation/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/../test/diRunable/*.php'));

de\any\di\reflection\klass\standard::$cache = new de\any\di\cache\void();

ini_set('apc.enable_cli', true);

$rows = 10000;

$microtime = microtime(true);
for($i=0; $i < $rows; $i++) {
    $nested_object = new nested_object();
    $nested_nestedservice1 = new nested_nestedservice1();
    $nested_object->setNestedService1($nested_nestedservice1);

    $foo = new diDecorateDecoratorNested1(new diDecorateDecorator1(new diDecorateStd1()));

}
echo "\nnative: ".(microtime(true)-$microtime)."\n";

$microtime = microtime(true);
$di = new \de\any\di();
$di->bind('nested_iobject')->to('nested_object');
$di->bind('nested_inestedservice1')->to('nested_nestedservice1');

$di->bind('istd')->to('diDecorateStd1');
$di->bind('istd')->to('diDecorateDecorator1')->decorated(true);
$di->bind('istd')->to('diDecorateDecoratorNested1')->decorated(true);
$di->bind('nested_inestedservice1')->to('nested_nestedservice1');
for($i=0; $i < $rows; $i++) {
    $di->get('nested_iobject')->getNestedService1();
    $di->get('istd')->getService();
}
echo "\nvoid: ".(microtime(true)-$microtime)."\n";


de\any\di\reflection\klass\standard::$cache = new de\any\di\cache\memory();

$microtime = microtime(true);
$di = new \de\any\di();
$di->bind('nested_iobject')->to('nested_object');
$di->bind('nested_inestedservice1')->to('nested_nestedservice1');

$di->bind('istd')->to('diDecorateStd1');
$di->bind('istd')->to('diDecorateDecorator1')->decorated(true);
$di->bind('istd')->to('diDecorateDecoratorNested1')->decorated(true);
$di->bind('nested_inestedservice1')->to('nested_nestedservice1');
for($i=0; $i < $rows; $i++) {
    $di->get('nested_iobject')->getNestedService1();
    $di->get('istd')->getService();
}
echo "\nmemory: ".(microtime(true)-$microtime)."\n";

/*
de\any\di\reflection\klass\standard::$cache = new de\any\di\cache\file();

$microtime = microtime(true);
$di = new \de\any\di();
$di->bind('nested_iobject')->to('nested_object');
$di->bind('nested_inestedservice1')->to('nested_nestedservice1');
for($i=0; $i < $rows; $i++) {
    $di->get('nested_iobject')->getNestedService1();
}
echo "\nfile: ".(microtime(true)-$microtime)."\n";

echo "\nmemory: ".(microtime(true)-$microtime)."\n";
*/

de\any\di\reflection\klass\standard::$cache = new de\any\di\cache\apc();

$microtime = microtime(true);
$di = new \de\any\di();
$di->bind('nested_iobject')->to('nested_object');
$di->bind('nested_inestedservice1')->to('nested_nestedservice1');

$di->bind('istd')->to('diDecorateStd1');
$di->bind('istd')->to('diDecorateDecorator1')->decorated(true);
$di->bind('istd')->to('diDecorateDecoratorNested1')->decorated(true);
$di->bind('nested_inestedservice1')->to('nested_nestedservice1');
for($i=0; $i < $rows; $i++) {
    $di->get('nested_iobject')->getNestedService1();
    $di->get('istd')->getService();
}
echo "\napc: ".(microtime(true)-$microtime)."\n";

de\any\di\reflection\klass\standard::$cache = new de\any\di\cache\memApc();

$microtime = microtime(true);
$di = new \de\any\di();
$di->bind('nested_iobject')->to('nested_object');
$di->bind('nested_inestedservice1')->to('nested_nestedservice1');

$di->bind('istd')->to('diDecorateStd1');
$di->bind('istd')->to('diDecorateDecorator1')->decorated(true);
$di->bind('istd')->to('diDecorateDecoratorNested1')->decorated(true);
$di->bind('nested_inestedservice1')->to('nested_nestedservice1');

for($i=0; $i < $rows; $i++) {
    $di->get('nested_iobject')->getNestedService1();
    $di->get('istd')->getService();
}
echo "\nmemApc: ".(microtime(true)-$microtime)."\n";