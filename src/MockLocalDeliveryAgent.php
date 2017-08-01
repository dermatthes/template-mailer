<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 7/20/15
 * Time: 9:35 PM
 */
namespace Leuffen\TemplateMailer;



class MockLocalDeliveryAgent implements MailDeliveryAgent {

    public $lastMail;

    public function send(MailBody $mail) {
        $mail->render($mailData);
        $this->lastMail = $mailData;
    }
}