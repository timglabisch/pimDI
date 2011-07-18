<?php
namespace de\any\di\test\example\getterInjection\withoutDiC;

class RegistrationService implements iRegistrationService {

    private $mailService = null;

    function sendDOIMail($email) {
        $this->getMailService()->sendTo($email, 'Bitte ...');
    }

    public function setMailService(iMailer $mailService)
    {
        $this->mailService = $mailService;
    }

    public function getMailService()
    {
        if($this->mailService == null)
            $this->mailService = new MailService();

        return $this->mailService;
    }
}