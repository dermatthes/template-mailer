<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 21.07.15
 * Time: 18:41
 */

use de\leuffen\template_mailer\MailBody;
use de\leuffen\template_mailer\MailPart;

require __DIR__ . "/../../src/autoloader.inc.php";

$fn = function ($toEmail) {

    $body = new MailBody($toEmail, "[template-mailer] New MultiPartHtmlMail Subject");
    $body->addPart(new MailPart("Some fallback text content for text-only readers."));
    $body->addPart(new MailPart("<h1>This is Html</h1><p>With nice formatting</p>", "text/html"));
    $body->send();


    echo "<span>Mail successfully sent</span>";
};

if (isset ($_POST["email"]))
    $fn ($_POST["email"]);

?>
<h1>MultiPart Html Demo 1</h1>
<form action="#" method="POST">
    <label for="email">E-Mail:</label>
    <input type="email" name="email" placeholder="your@address">
    <button type="submit">Send Mail</button>
</form>
