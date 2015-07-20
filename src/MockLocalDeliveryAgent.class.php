<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 7/20/15
 * Time: 9:35 PM
 */
namespace de\leuffen\template_mailer;



class MockLocalDeliveryAgent implements MailDeliveryAgent {



    public function getLastMail () {

    }

    public function getLastMails () {

    }

    public function clear () {

    }

    public function send(MailBody $mail) {

    }
}