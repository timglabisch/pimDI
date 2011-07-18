<?php
namespace de\any\di\test\example\getterInjection\diC;

interface iMailer {
    function sendTo($email, $content);
}