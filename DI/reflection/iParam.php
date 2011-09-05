<?
namespace de\any\di\reflection;

interface iParam {
    public function getConcern();
    public function setConcern($concern);
    public function getInterface();
    public function setInterface($interface);
    public function setInject($inject);
    public function getInject();
}