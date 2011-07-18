<?php
namespace de\any\di\test\example\getterInjection\diC;

class RegistrationService implements iRegistrationService {

    private $mailService = null;

    function sendDOIMail($email) {
        $this->mailService->sendTo($email, 'Bitte ...');
    }

    /**
     * @inject
     */
    public function setMailService(iMailer $mailService) {
        $this->mailService = $mailService;
    }
}