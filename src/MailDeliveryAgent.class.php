<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 7/20/15
 * Time: 9:28 PM
 */

namespace de\leuffen\template_mailer;


interface MailDeliveryAgent {

    public function send (MailBody $mail);

}