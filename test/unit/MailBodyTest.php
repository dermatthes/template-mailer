<?php
use de\leuffen\template_mailer\exception\InvalidEMailAddressException;
use de\leuffen\template_mailer\MailBody;
use de\leuffen\template_mailer\MailPart;

/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 21.07.15
 * Time: 17:41
 */


    class MailBodyTest extends PHPUnit_Framework_TestCase {


        public function testInvalidEMailAddress () {
            $this->setExpectedException(InvalidEMailAddressException::class);

            $body = new MailBody();
            $body->addTo("Matthias Leuffen <matthes@ leuffen.de>");
        }

        public function testSinglePartMessage () {
            $body = new MailBody();
            $body->addTo("Matthias Leuffen <matthes@leuffen.de>");
            $body->setSubject("Test Mail");
            $body->addPart(new MailPart("Some Data"));

            echo $body->render();

        }

        public function testMultiPartMessage () {
            $body = new MailBody();
            $body->addTo("Matthias Leuffen <matthes@leuffen.de>");
            $body->setSubject("Test Mail");
            $body->addPart(new MailPart("Some Data"));
            $body->addPart(new MailPart("Some other Data"));
            echo $body->render();

        }
    }