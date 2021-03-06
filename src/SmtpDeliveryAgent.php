<?php
    /**
     * Created by PhpStorm.
     * User: mike
     * Date: 13.06.18
     * Time: 17:14
     */

    namespace Leuffen\TemplateMailer;


    use Leuffen\TemplateMailer\Exception\MailException;
    use Leuffen\TemplateMailer\Exception\MailTemplateException;
    use Leuffen\TemplateMailer\Exception\SmtpDeliveryException;
    use PHPMailer\PHPMailer\PHPMailer;

    class SmtpDeliveryAgent implements MailDeliveryAgent {


        /**
         * Semicolon getrennte Liste von mit main und backup SMTP Servern
         *
         * @var string
         */
        private $mHost;
        private $mUsername;
        private $mPassword;
        private $mSmtpDebug;
        private $mSmtpAuth;
        private $mSmtpSecure;
        private $mPort;

        /**
         * @var MailPart
         */
        private $mMailContent;


        public function __construct($host, $port = "25", $username = "", $password = "", $smtpDebug = 0, $smtpAuth = false, $smtpSecure = "tls") {
            $this->mHost = $host;
            $this->mUsername = $username;
            $this->mPassword = $password;
            $this->mSmtpDebug = $smtpDebug;
            $this->mSmtpAuth = $smtpAuth;
            $this->mSmtpSecure = $smtpSecure;
            $this->mPort = $port;
        }

        /**
         * @param MailBody $mailBody
         * @throws MailException
         * @throws SmtpDeliveryException
         */
        public function send (MailBody $mailBody) {

            $mailBody->render($mailData);

            $this->mMailContent = $mailBody->getMailParts()[0];

            $partData = $this->mMailContent->__getIntData();

            $phpMailer = new PHPMailer(TRUE);

            //Temporary take charset from first mailpart
            //TODO: Needs to be refactored
            if ( ! is_null($partData['charset']) ) {
                $phpMailer->CharSet = $partData['charset'];
            }

            $phpMailer->SMTPAutoTLS = false;
            try {
                //Server settings
                $phpMailer->SMTPDebug = $this->mSmtpDebug;              // Enable verbose debug output
                $phpMailer->isSMTP();                                   // Set mailer to use SMTP
                $phpMailer->Host = $this->mHost;                        // Specify main and backup SMTP servers
                $phpMailer->SMTPAuth = $this->mSmtpAuth;                // Enable SMTP authentication
                $phpMailer->Username = $this->mUsername;                // SMTP username
                $phpMailer->Password = $this->mPassword;                // SMTP password
                //$phpMailer->SMTPSecure = $this->mSmtpSecure;          // Enable TLS encryption, `ssl` also accepted
                $phpMailer->Port = $this->mPort;                        // TCP port to connect to

                //Recipients
                $from = $mailBody->getFrom()->render();
                $phpMailer->setFrom($from);
                foreach ($mailBody->getTo() as $item) {
                    $phpMailer->addAddress($item->render());
                }

                if ($mailBody->getReplyTo() !== NULL ) {
                    $phpMailer->addReplyTo($mailBody->getReplyTo()->render());
                }

                if ($mailBody->getCC() != []) {
                    $phpMailer->addCC($mailBody->_getEMailString($mailBody->getCC()));
                }


                //Content
                if ($partData["contentType"] == "text/html") {
                    $phpMailer->isHTML(TRUE);
                }

                $phpMailer->Subject = $mailBody->getSubject();
                $phpMailer->Body    = $partData["content"];

                $phpMailer->send();

            } catch (\Exception $e) {
                throw new SmtpDeliveryException("Error on Smtp delivery",0, $e);
            }
        }
    }