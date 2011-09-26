<?php
namespace test\diExamples\repository;

require_once __DIR__.'/_iUser.php';
require_once __DIR__.'/user.php';
require_once __DIR__.'/customRepository.php';

class basicUserRepositoryTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->di = new \de\any\di();
        $this->di->bind('test\diExamples\repository\iUser')->to('test\diExamples\repository\user');
    }

    public function testRepositoryIsArray() {
        $this->assertInstanceOf('ArrayObject', $this->di->get('test\diExamples\repository\iUser[]'));
    }

    public function testRepositoryAppend() {
        $repository = $this->di->get('test\diExamples\repository\iUser[]');
        $repository->append($this->di->get('test\diExamples\repository\iUser'));
        $repository->append($this->di->get('test\diExamples\repository\iUser'));
        $this->assertEquals(count($repository), 2);
    }

    public function testRepositoryAppendNonShared() {
        $repository = $this->di->get('test\diExamples\repository\iUser[]');
        $repository->append($this->di->get('test\diExamples\repository\iUser'));
        $repository->append($this->di->get('test\diExamples\repository\iUser'));
        $this->assertEquals(count($repository), 2);
        $this->assertEquals(count($this->di->get('test\diExamples\repository\iUser[]')), 0);
    }

    public function testRepositoryAppendShared() {
        $this->di->bind('test\diExamples\repository\iUser[]')->shared(true);
        $repository = $this->di->get('test\diExamples\repository\iUser[]');
        $repository->append($this->di->get('test\diExamples\repository\iUser'));
        $repository->append($this->di->get('test\diExamples\repository\iUser'));
        $this->assertEquals(count($repository), 2);
        $this->assertEquals(count($this->di->get('test\diExamples\repository\iUser[]')), 2);
    }

    public function testRepositoryChangeRepositoryImplementation() {
        $this->di->bind('test\diExamples\repository\iUser[]')->to('test\diExamples\repository\customRepository');
        $this->assertEquals(count($this->di->get('test\diExamples\repository\iUser[]')), 99);
    }

}