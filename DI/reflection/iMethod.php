<?
namespace de\any\di\reflection;

interface iMethod {
    public function getParameters();
    public function setParameters(array $params);
    public function invokeArgs($instance, $args);
    public function setInject($inject);
    public function getInject();
}