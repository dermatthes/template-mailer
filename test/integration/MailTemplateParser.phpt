<?php


/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 17.07.15
 * Time: 15:55
 */
namespace Leuffen\TemplateMailer;

require __DIR__ . "/../../vendor/autoload.php";


use Tester\Assert;



\Tester\Environment::setup();


$dirs = glob(__DIR__ . "/tpls/*");
$tt = new MailTemplateParser();
$tt->__setFixedBoundary();

foreach ($dirs as $dir) {
    echo "\nTesting $dir...";
    $tt->loadTemplate(file_get_contents($dir . "/_in.txt"));
    $data = require ($dir . "/_in.php");
    $mail = $tt->apply($data);
    Assert::equal(str_replace("\n", "\r\n", file_get_contents($dir . "/out.txt")), $mail->render(), "Error in check: {$dir}");
}

