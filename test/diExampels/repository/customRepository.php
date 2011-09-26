<?php
namespace test\diExamples\repository;

class customRepository extends \de\any\di\repository\standard {
    public function count() {
        return 99;
    }
}