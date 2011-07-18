<?php

namespace de\any\di\test\example\getterInjection\diC;
use de\any\di;

require_once __DIR__.'/iMailer.php';
require_once __DIR__.'/iRegistration.php';
require_once __DIR__.'/Mailer.php';
require_once __DIR__.'/Registration.php';

class RegistrationTest extends \PHPUnit_Framework_TestCase {

    function testSendDOIMail() {


        $mock = $this->getMock('de\any\di\test\example\getterInjection\diC\MailService');
        $mock->expects($this->once())
            ->method('sendTo')
            ->with('test@test.de', 'Bitte ...');

        $di = new di();
        $di->bind('de\any\di\test\example\getterInjection\diC\iMailer')->to($mock);
        $di->bind('de\any\di\test\example\getterInjection\diC\iRegistrationService')->to('de\any\di\test\example\getterInjection\diC\RegistrationService');

        $registration = $di->get('de\any\di\test\example\getterInjection\diC\iRegistrationService');
        $registration->sendDOIMail('test@test.de');
    }

}
