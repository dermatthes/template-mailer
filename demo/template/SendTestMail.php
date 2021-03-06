<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 7/21/15
 * Time: 10:40 PM
 */

require __DIR__ . "/../../vendor/autoload.php";



$fn = function ($toEmail) {
    $data = require(__DIR__ . "/_data.php");
    $data["recipient"] = $toEmail;

    $parser = new \Leuffen\TemplateMailer\MailTemplateParser();
    $parser->loadTemplate(file_get_contents("tpl/".basename($_POST["template"])));
    $parser->send($data);

    echo "<span>Mail successfully sent</span>";
};

if (isset ($_POST["email"]))
    $fn ($_POST["email"]);

?>
<h1>Simple Mail Demo 1</h1>
<form action="#" method="POST">
    <select name="template">
        <?PHP
        foreach (glob (__DIR__ . "/tpl/*.txt") as $template):
            $template = basename($template);
            $selected = "";
            if ($template == $_POST["template"])
                $selected = "selected"
        ?>
        <option <?= $selected ?> value="<?= $template ?>"><?= $template; ?></option>
        <? endforeach?>
    </select>
    <label for="email">E-Mail:</label>
    <input type="email" name="email" placeholder="your@address" value="<?= htmlspecialchars(@$_POST["email"]); ?>">
    <button type="submit">Send Mail</button>
</form>
