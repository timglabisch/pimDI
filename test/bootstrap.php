<?php

require_once __DIR__ . '/../di.php';

array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diTest/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diNestedTest/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diConstructor/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diDecorateTest/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diDecoratorNeedDecorated/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diSharedDecorators/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diParam/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diCircular/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diCircularNested/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diPropertyParseException/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diTestIgnoreAnnotation/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diRunable/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diRepositoryConcern/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diRepositoryInject/*.php'));
array_map(function($v) { include_once  $v; }, glob(__DIR__.'/diCodingstyle/*.php'));
