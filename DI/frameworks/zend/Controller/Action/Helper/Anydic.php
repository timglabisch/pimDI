<?php
namespace de\any\di\frameworks\zend\Controller\Action\Helper;

class Anydic extends \Zend\Controller\Action\Helper\AbstractHelper {
    public function init() {
        try {
        require_once APPLICATION_PATH.'/../library/pimDI/di.php';
        $di = new \de\any\di();
        $di->bind('\Foo\iFoo')->to('\Foo\Foo');
        $di->justInject($this->getActionController());
        } catch(Exception $e) {
            var_dump($e);
            die();
        }
    }
}