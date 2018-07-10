<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 7/20/15
 * Time: 9:29 PM
 */

namespace Leuffen\TemplateMailer;


use Leuffen\TemplateMailer\Exception\MailException;


class PhpLocalDeliveryAgent implements MailDeliveryAgent {


    /**
     * @param MailBody $mail
     * @throws MailException
     */
    public function send(MailBody $mail) {
        $mail->render($mailData);
        if ( ! mail ($mailData["To"], $mailData["Subject"], $mailData["content"], $mailData["headers"])) {
            throw new MailException("Cannot send mail. mail() reported error. Check local delivery agent.");
        }

    }

}
