<?php
namespace de\any\di\frameworks\zend\Application\Bootstrap\Helper;

class Anydic {
    public function init(\Zend\Application\Bootstrap $bootstrap) {
        require_once APPLICATION_PATH.'/../library/pimDI/DI/frameworks/zend/Controller/Action/Helper/Anydic.php';

        $bootstrap->bootstrap('FrontController');
        $bootstrap->frontcontroller->getHelperBroker()->register('dic', new \de\any\di\frameworks\zend\Controller\Action\Helper\Anydic());
        $bootstrap->getApplication()->getAutoloader()->setFallbackAutoloader(true);
    }
}