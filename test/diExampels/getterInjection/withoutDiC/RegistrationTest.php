<?php

namespace de\any\di\test\example\getterInjection\withoutDiC;
use de\any\di;

require_once __DIR__.'/iMailer.php';
require_once __DIR__.'/iRegistration.php';
require_once __DIR__.'/Mailer.php';
require_once __DIR__.'/Registration.php';

class RegistrationTest extends \PHPUnit_Framework_TestCase {

    function testGetMailService() {
        $registration = new RegistrationService();
        $this->assertInstanceOf('\de\any\di\test\example\getterInjection\withoutDiC\iMailer',$registration->getMailService());
    }

    function testGetSetMailService() {
        $mock = $this->getMock('\de\any\di\test\example\getterInjection\withoutDiC\MailService');
        $registration = new RegistrationService();

        $registration->setMailService($mock);

        $this->assertTrue($mock === $registration->getMailService());
    }

    function testSendDOIMail() {
        $mock = $this->getMock('\de\any\di\test\example\getterInjection\withoutDiC\MailService');
        $mock->expects($this->once())
            ->method('sendTo')
            ->with('test@test.de', 'Bitte ...');

        $registration = new RegistrationService();
        $registration->setMailService($mock);
        $registration->sendDOIMail('test@test.de');
    }

}
