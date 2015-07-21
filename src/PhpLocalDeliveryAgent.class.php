<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 7/20/15
 * Time: 9:29 PM
 */

namespace de\leuffen\template_mailer;



use de\leuffen\template_mailer\exception\MailException;

class PhpLocalDeliveryAgent implements MailDeliveryAgent {


    public function send(MailBody $mail) {
        $mail->render($mailData);
        if ( ! mail ($mailData["To"], $mailData["Subject"], $mailData["content"], $mailData["headers"]))
            throw new MailException("Cannot send mail. mail() reported error. Check local delivery agent.");

    }
}