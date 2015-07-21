<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 21.07.15
 * Time: 17:33
 */

namespace de\leuffen\template_mailer;


spl_autoload_register(function ($class) {
    if (substr($class, 0, strlen(__NAMESPACE__)) != __NAMESPACE__)
        return;
    $path = __DIR__ . "/" . str_replace("\\", "/", substr($class, strlen(__NAMESPACE__))) . ".class.php";
    require_once($path);
});