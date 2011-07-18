<?php

namespace de\any\di\test\example\getterInjection\diC;
use de\any\di;

require_once __DIR__.'/iMailer.php';
require_once __DIR__.'/iRegistration.php';
require_once __DIR__.'/Mailer.php';
require_once __DIR__.'/Registration.php';

class MailerTest extends \PHPUnit_Framework_TestCase {

    function testSendTo() {

        $mailer = new MailService();
        $this->assertEquals('send mail to test@test.de with content test', $mailer->sendTo('test@test.de', 'test'));

    }

}
