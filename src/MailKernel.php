<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 7/20/15
 * Time: 9:30 PM
 */


namespace Leuffen\TemplateMailer;


class MailKernel {

    const VERSION = "1.0.1";

    const EOL = "\r\n";

    /**
     * @var MailDeliveryAgent[]
     */
    public static $sMailDeliveryAgent = [];

    public static $sGlobalByPassTo = FALSE;

    /**
     * Register a globally available MailDeliveryAgent
     *
     * The MailDeliveryAgent is responsible for transmitting the application mail to a real-world
     * or mock mailserver.
     *
     * @param MailDeliveryAgent $agent
     * @param string $alias
     */
    public static function SetMailDeliveryAgent (MailDeliveryAgent $agent, $alias="default") {
        self::$sMailDeliveryAgent[$alias] = $agent;
    }


    /**
     * If you specify a eMail Address here, all E-Mails will be redirected
     * to this address instead of their original targets.
     *
     * This is very usefull for development or testing, when you don't want
     * any of your mails leave the sandbox Environment.
     *
     * You'll find the original headers in X-Bypass-Orig-XYZ - Headers.
     *
     * Select FALSE to disable this functionality
     *
     * @param $globalToAddress
     */
    public static function SetGlobalBypass ($globalToAddress) {
        self::$sGlobalByPassTo = $globalToAddress;
    }


    public static function GetGlobalBypass () {
        return self::$sGlobalByPassTo;
    }

    /**
     *
     * @param string $alias
     * @return MailDeliveryAgent
     */
    public static function GetMailDeliveryAgent ($alias="default") {
        if ( ! isset (self::$sMailDeliveryAgent[$alias])) {
            if ($alias != "default") {
                throw new \InvalidArgumentException("No MailDeliveryAgent registered with alias '$alias'. Check the mailsystem was properly initialized.");
            }
            self::$sMailDeliveryAgent["default"] = new PhpLocalDeliveryAgent();
        }
        return self::$sMailDeliveryAgent[$alias];
    }

}
