<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 7/20/15
 * Time: 9:25 PM
 */

namespace Leuffen\TemplateMailer;


use Leuffen\TemplateMailer\Exception\MailTemplateException;
use Leuffen\TextTemplate\TextTemplate;

class MailTemplateParser {

    /**
     * @var TextTemplate
     */
    private $mTextTemplate;


    private $mTemplate = NULL;

    private $mFixedBoundary = FALSE;

    public function __construct (TextTemplate $textTemplate=NULL) {
        if ($textTemplate === NULL) {
            $textTemplate = new TextTemplate();
        }
        $this->mTextTemplate = $textTemplate;
    }


    /**
     * Return the TextTemplate object
     *
     * @return TextTemplate
     */
    public function getTextTemplateParser () {
        return $this->mTextTemplate;
    }


    /**
     * Load a string template
     *
     * @param $template
     * @return $this
     */
    public function loadTemplate ($template) {
        $this->mTemplate = $template;
        return $this;
    }

    /**
     * Unit - Testing only
     *
     *
     */
    public function __setFixedBoundary () {
        $this->mFixedBoundary = TRUE;
    }


    /**
     * @param MailBody $mailBody
     * @param $headerStr
     * @param $data
     * @throws Exception\InvalidEMailAddressException
     * @throws \Leuffen\TextTemplate\TemplateParsingException
     */
    private function _parseHeader (MailBody $mailBody, $headerStr, $data) {
        $textTemplate = clone $this->mTextTemplate;
        $textTemplate->addFilter("_DEFAULT_", function ($input) {
            // Replace evil characters in header lines
            $input = str_replace("\r\n", " ", $input);
            $input = str_replace("\r", "", $input);
            $input = str_replace("\n", " ", $input);
            return substr($input, 0, 255);
        });

        $textTemplate->addFilter("singleemail", function ($input) {
            $input = (string)$input;
            $input = str_replace(";", ",", $input);
            $emails = explode(",", $input);
            return $emails[0];
        });

        $parsedHeader = $textTemplate->loadTemplate($headerStr)->apply($data);

        $lines = explode("\n", $parsedHeader);
        foreach ($lines as $line) {

            $line = trim ($line);
            if (strpos($line, ":") === FALSE) {
                continue;
            }

            list ($origHeaderName, $headerValue) = explode(":", $line, 2);
            $origHeaderName = trim($origHeaderName);
            $headerValue = trim($headerValue);
            $ucHeaderName = strtoupper($origHeaderName);
            switch ($ucHeaderName) {
                case "TO":
                    $recipients = str_replace(";", ",", $headerValue);
                    $recipients = explode(",", $recipients);

                    foreach ($recipients as $curRecipient) {
                        $mailBody->addTo($curRecipient, TRUE);
                    }
                    break;

                case "BCC":
                    $recipients = str_replace(";", ",", $headerValue);
                    $recipients = explode(",", $recipients);
                    foreach ($recipients as $curRecipient) {
                        $mailBody->addBcc($curRecipient, TRUE);
                    }
                    break;

                case "CC":
                    $recipients = str_replace(";", ",", $headerValue);
                    $recipients = explode(",", $recipients);
                    foreach ($recipients as $curRecipient) {
                        $mailBody->addCc($curRecipient);
                    }
                    break;

                case "CONTENT-TYPE":
                    $mailBody->setContentType($headerValue);
                    break;

                case "SUBJECT":
                    $mailBody->setSubject($headerValue);
                    break;

                case "FROM":
                    $mailBody->setFrom($headerValue);
                    break;

                case "CONTENT-TRANSFER-ENCODING":
                    $mailBody->setContentTransferEncoding($headerValue);
                    break;

                default:
                    if (trim ($origHeaderName) != "" && trim  ($headerValue) != "") {
                        $mailBody->addHeader($origHeaderName, $headerValue);
                    }
            }
        }
    }


    /**
     * @param $xmlAttribStr
     * @return array
     * @throws MailTemplateException
     */
    private function _getOptions ($xmlAttribStr) {
        $ret = [];
        $rest = preg_replace_callback('/([a-z0-9]+)=([\"\']?)(.*?)\2/i',
            function ($matches) use (&$ret) {
                $ret[strtoupper($matches[1])] = $matches[3];
                return "";
            }, $xmlAttribStr);

        if (trim ($rest) != "")
            throw new MailTemplateException("Cannot parse attributes from <mailPart>: '$xmlAttribStr': Unparsed extra content after parsing: '$rest'");
        return $ret;
    }


    /**
     * @param MailBody $mailBody
     * @param $bodyString
     * @param $data
     * @throws \Leuffen\TextTemplate\TemplateParsingException
     */
    private function _parseBody (MailBody $mailBody, $bodyString, $data) {

        if (preg_match ('/\<mailPart.*\>/im', $bodyString)) {
            // Multipart-Body
            $rest = preg_replace_callback('/\<mailPart (.*?)\>\n?(.*?)\n?\<\/mailPart\>/ims',
                function ($matches) use ($mailBody, $data) {
                    $allowedAttribs = ["CONTENTTYPE", "CHARSET", "CONTENTTRANSFERENCODING", "CONTENTDISPOSITION", "FILENAME", "TOKEN", "ID", "SKIPENCODING"];

                    $attribs = $this->_getOptions($matches[1]);
                    foreach ($attribs as $curKey => $curValue) {
                        if ( ! in_array(strtoupper($curKey), $allowedAttribs))
                            throw new MailTemplateException("Unknown <mailPart> - Attribute '$curKey=\"$curValue\"' in '{$matches[1]}': Allowed are " . implode (", ", $allowedAttribs));

                    }

                    $content = $matches[2];

                    $mailPart = new MailPart();



                    $contentType = "text/plain";
                    if (isset ($attribs["CONTENTTYPE"])) {
                        $mailPart->setContentType($contentType = $attribs["CONTENTTYPE"]);
                    }
                    if (isset ($attribs["CHARSET"])) {
                        $mailPart->setCharset($attribs["CHARSET"]);
                    }
                    if (isset ($attribs["CONTENTDISPOSITION"])) {
                        $mailPart->setContentDisposition($attribs["CONTENTDISPOSITION"]);
                    }
                    if (isset ($attribs["FILENAME"])) {
                        $mailPart->setContentDispositionFileName($attribs["FILENAME"]);
                    }
                    if (isset ($attribs["TOKEN"])) {
                        $mailPart->setContentDispositionToken($attribs["TOKEN"]);
                    }
                    if (isset ($attribs["ID"])) {
                        $mailPart->setContentId($attribs["ID"]);
                    }

                    $contentTransferEncoding = "8Bit";
                    if (isset ($attribs["CONTENTTRANSFERENCODING"])) {
                        $contentTransferEncoding = $attribs["CONTENTTRANSFERENCODING"];
                    }
                    $mailPart->setContentTransferEncoding($contentTransferEncoding);

                    // Parse the content

                    $textTemplate = clone $this->mTextTemplate;

                    if (strtoupper($contentType) == "TEXT/HTML") {
                        // Set default filter to HTML-Escaping
                        $textTemplate->addFilter("_DEFAULT_", function ($input) {return htmlspecialchars($input); });
                    }

                    if (strtoupper($contentType) != "TEXT/HTML") {
                        // No filter on other content-types
                        $textTemplate->addFilter("_DEFAULT_", function ($input) { return $input; });
                    }

                    $content = $textTemplate->loadTemplate($content)->apply($data);

                    if ( ! isset ($attribs["SKIPENCODING"]) || strtoupper($attribs["SKIPENCODING"]) == "NO") {
                        $encoder = new MailContentTransferEncoder();
                        try {
                            $content = $encoder->encode($content, $contentTransferEncoding);
                        } catch (\InvalidArgumentException $e) {
                            throw new MailTemplateException("No contentTransferEncoder available for contentTransferEncoding='$contentTransferEncoding'");
                        }
                    }

                    $mailPart->setContent($content);
                    $mailBody->addPart($mailPart);
                    return "";
                }, $bodyString);

            if (trim ($rest) != "") {
                $part = new MailPart("Some fragments from template:\n$rest", "text/plain");
                $mailBody->addPart($part);
            }
        } else {
            // SingleMail Body
            $contentType = $mailBody->getContentType();
            $textTemplate = clone $this->mTextTemplate;
            if ($contentType == "text/html") {
                $textTemplate->addFilter("_DEFAULT_", function ($input) { return htmlspecialchars($input); });
            } else {
                $textTemplate->addFilter("_DEFAULT_", function ($input) { return $input; });
            }
            $textTemplate->loadTemplate($bodyString);
            $content = $textTemplate->apply($data);

            $contentTransferEncoding = $mailBody->getContentTransferEncoding();
            if ($contentTransferEncoding === NULL) {
                $mailBody->setContentTransferEncoding("8Bit");
            }
            $encode = new MailContentTransferEncoder();

            $content = $encode->encode($content, $mailBody->getContentTransferEncoding());

            $mailPart = new MailPart($content, $contentType);
            $mailPart->setContentTransferEncoding($mailBody->getContentTransferEncoding());
            $mailBody->addPart($mailPart);
        }
    }


    /**
     * @param $template
     * @param $header
     * @param $body
     * @throws MailTemplateException
     */
    private function _splitTemplate ($template, &$header, &$body) {
        $headerPos = strpos ($template, "\n\n");
        if ($headerPos === FALSE) {
            $headerPos = strpos($template, "\r\n\r\n");
        }

        if ($headerPos === FALSE) {
            throw new MailTemplateException("No mail header found in mail template: '{$template}'");
        }

        $header = substr ($template, 0, $headerPos);
        $body = substr ($template, $headerPos+2);
    }

    /**
     * @param $data
     * @return MailBody
     * @throws Exception\InvalidEMailAddressException
     * @throws MailTemplateException
     * @throws \Leuffen\TextTemplate\TemplateParsingException
     */
    public function apply ($data) {
        $template = str_replace("\r\n", "\n", $this->mTemplate);
        $this->_splitTemplate($template, $header, $body);

        $mail = new MailBody();
        if ($this->mFixedBoundary) {
            $mail->__setFixedBoundary("--=_NextPart_000_UNIT_TESTING_BOUNDARY_000");
        }
        $this->_parseHeader($mail,$header, $data);
        $this->_parseBody($mail, $body, $data);
        return $mail;
    }

    /**
     * Shortcut for
     *
     * MailTemplateParser->apply()->send()
     *
     * @param $data
     * @return MailBody
     * @throws Exception\InvalidEMailAddressException
     * @throws MailTemplateException
     * @throws \Leuffen\TextTemplate\TemplateParsingException
     */
    public function send ($data) {
        $mailBody = $this->apply($data);
        $mailBody->send();
        return $mailBody;
    }

}
