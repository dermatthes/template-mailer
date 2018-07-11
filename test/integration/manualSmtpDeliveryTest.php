<?php
    /**
     * Created by PhpStorm.
     * User: mike
     * Date: 14.06.18
     * Time: 17:23
     */

    namespace Leuffen\TemplateMailer;

    use Leuffen\TextTemplate\TextTemplate;

    require __DIR__ . "/../../vendor/autoload.php";


    $dir = __DIR__ . "/tpls/03_manualSmtpTestTextMail";

    $mailDeliveryAgent = new SmtpDeliveryAgent("localhost");
    MailKernel::SetMailDeliveryAgent($mailDeliveryAgent);

    $templateParser = new MailTemplateParser();
    $templateParser->loadTemplate(file_get_contents($dir . "/_in.txt"));
    $templateParser->__setFixedBoundary();

    $data  = require($dir . "/_in.php");

    $mail = $templateParser->apply($data);

    $soll = str_replace("\n", "\r\n", file_get_contents($dir . "/out.txt"));
    $ist = $mail->render();

    $mail->send();

    if ($soll !== $ist) {
        throw new \Exception("Das war wohl nix");
    }

    echo "fertig\n";