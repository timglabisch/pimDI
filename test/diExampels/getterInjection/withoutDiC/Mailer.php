<?php
namespace de\any\di\test\example\getterInjection\withoutDiC;

class MailService implements iMailer {

    function sendTo($email, $content) {
        return 'send mail to '. $email .' with content '.$content;
    }
}