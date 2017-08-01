<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 01.08.17
 * Time: 10:50
 */

namespace Leuffen\TemplateMailer;
use Leuffen\TemplateMailer\Exception\InvalidEMailAddressException;
use Tester\Assert;

require __DIR__ . "/../../vendor/autoload.php";
\Tester\Environment::setup();


$template = <<<EOT
To: matthes@leuffen.de
Subject: [template-mailer] Unit Test Testmail
From: template-mailer@leuffen.de

<mailPart contentType="text/plain" CHARSET="UTF-8">
Plain Text
</mailPart>
<mailPart contentType="text/html" CHARSET="UTF-8">
Crazy <b>Html</b> Stuff
</mailPart>
EOT;


Assert::noError(function () use ($template) {
    MailKernel::SetMailDeliveryAgent(new MockLocalDeliveryAgent());
    $parser = new MailTemplateParser();
    $parser->loadTemplate($template);
    $parser->send ([]);

    Assert::equal("matthes@leuffen.de", MailKernel::GetMailDeliveryAgent()->lastMail["To"]);
    Assert::equal("[template-mailer] Unit Test Testmail", MailKernel::GetMailDeliveryAgent()->lastMail["Subject"]);
});