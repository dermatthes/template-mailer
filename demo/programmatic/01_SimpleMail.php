<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 21.07.15
 * Time: 18:41
 */


require __DIR__ . "/../../vendor/autoload.php";

$fn = function ($toEmail) {

    $body = new \Leuffen\TemplateMailer\MailBody($toEmail, "[template-mailer] New SimpleMailDemo Subject");
    $body->addPart(new \Leuffen\TemplateMailer\MailPart("Some text content"));
    $body->send();
    echo "<span>Mail successfully sent</span>";
};

if (isset ($_POST["email"]))
    $fn ($_POST["email"]);

?>
<h1>Simple Mail Demo 1</h1>
<form action="#" method="POST">
    <label for="email">E-Mail:</label>
    <input type="email" name="email" placeholder="your@address">
    <button type="submit">Send Mail</button>
</form>
