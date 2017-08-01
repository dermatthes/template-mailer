<?php


/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 21.07.15
 * Time: 17:41
 */
namespace Leuffen\TemplateMailer;
use Leuffen\TemplateMailer\Exception\InvalidEMailAddressException;
use Tester\Assert;

require __DIR__ . "/../../vendor/autoload.php";
\Tester\Environment::setup();



Assert::exception(function () {
    $body = new MailBody();
    $body->addTo("Matthias Leuffen <matthes@ leuffen.de>");
}, InvalidEMailAddressException::class);


Assert::noError(function () {
    $body = new MailBody();
    $body->addTo("Matthias Leuffen <matthes@leuffen.de>");
    $body->setSubject("Test Mail");
    $body->addPart(new MailPart("Some Data"));
    echo $body->render();
});

Assert::noError(function () {
    $body = new MailBody();
    $body->addTo("Matthias Leuffen <matthes@leuffen.de>");
    $body->setSubject("Test Mail");
    $body->addPart(new MailPart("Some Data"));
    $body->addPart(new MailPart("Some other Data"));
    echo $body->render();
});
