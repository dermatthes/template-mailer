<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 7/20/15
 * Time: 9:21 PM
 */


namespace Leuffen\TemplateMailer;


use Leuffen\TemplateMailer\Exception\InvalidEMailAddressException;
use Leuffen\TemplateMailer\Exception\MailException;

class MailBody {

    /**
     * @var EMailAddress
     */
    private $mFrom = NULL;

    /**
     * @var EMailAddress[]
     */
    private $mTo = [];

    /**
     * @var EMailAddress[]
     */
    private $mBcc = [];

    /**
     * @var EMailAddress[]
     */
    private $mCc = [];

    private $mSubject = NULL;

    private $mContentType = NULL;
    private $mCharset = NULL;

    private $mContentTransferEncoding = NULL;

    private $mHeader = [];

    /**
     * @var MailPart[]
     */
    private $mParts = [];


    private $mBoundary = NULL;


    private $mMailData = [];


    public function __construct ($toEMail=NULL, $subject=NULL, $from=NULL) {
        if ($toEMail !== NULL) {
            $this->addTo($toEMail);
        }
        if ($subject !== NULL) {
            $this->setSubject($subject);
        }
        if ($from !== NULL) {
            $this->setFrom($from);
        }
    }

    /**
     * For Unit-Testing only: Set a fixed Boundary
     *
     * @param $boundary
     */
    public function __setFixedBoundary ($boundary) {
        $this->mBoundary = $boundary;
    }






    public function __getBoundary () {
        // Use getmygid() and getmyinode() to rise entropy
        if ($this->mBoundary === NULL)
            $this->mBoundary = "----=_NextPart_" . strtoupper(md5(uniqid() . (string)getmypid() . (string)getmyinode()));
        return $this->mBoundary;
    }


    public function setHeader ($headerName, $value) {
        $this->mHeader[$headerName] = [$value];
    }

    /**
     * @param $headerName
     * @param $value
     * @return $this
     */
    public function addHeader ($headerName, $value) {
        if ( ! isset ($this->mHeader[$headerName])) {
            $this->mHeader[$headerName] = [ $value ];
            return $this;
        }
        if ( ! is_array($this->mHeader[$headerName])) {
            $this->mHeader[$headerName] = [ $this->mHeader[$headerName] ];
        } else {
            $this->mHeader[$headerName][] = $value;
        }
        return $this;
    }


    /**
     * Add Original Recipient
     *
     * @param $email
     * @return $this
     */
    public function addTo ($email, $softFail = FALSE) {
        try {
            if ( ! $email instanceof EMailAddress) {
                $email = new EMailAddress($email);
            }
        } catch (InvalidEMailAddressException $e) {
            if ( ! $softFail) {
                throw $e;
            }
            return $this;
        }
        $this->mTo[] = $email;
        return $this;
    }

    /**
     * Set BlindCopyRecipient
     *
     * @param $email
     * @return $this
     */
    public function addBcc ($email, $softFail = FALSE) {
        try {
            if ( ! $email instanceof EMailAddress) {
                $email = new EMailAddress($email);
            }
        } catch (InvalidEMailAddressException $e) {
            if ( ! $softFail) {
                throw $e;
            }
            return $this;
        }
        $this->mBcc[] = $email;
        return $this;
    }

    /**
     * Set the contentTransferEncoding of the envelope
     *
     * Available:
     * - 8BIT
     * - quoted-printable
     * - base64
     *
     * @param string $encoding
     * @return $this
     */
    public function setContentTransferEncoding ($encoding="quoted-printable") {
        $this->mContentTransferEncoding = $encoding;
        return $this;
    }

    public function getContentTransferEncoding () {
        return $this->mContentTransferEncoding;
    }

    /**
     * Set visual copy recipient
     *
     * @param $email
     * @return $this
     */
    public function addCc ($email, $softFail = FALSE) {
        try {
            if ( ! $email instanceof EMailAddress) {
                $email = new EMailAddress($email);
            }
        } catch (InvalidEMailAddressException $e) {
            if ( ! $softFail) {
                throw $e;
            }
            return $this;
        }
        $this->mCc[] = $email;
        return $this;
    }

    /**
     * Sets the Mail Subject
     *
     * @param $subject
     * @return $this
     */
    public function setSubject ($subject) {
        $this->mSubject = $subject;
        return $this;
    }

    public function setFrom ($email) {
        try {
            if ( ! $email instanceof EMailAddress) {
                $email = new EMailAddress($email);
            }
        } catch (InvalidEMailAddressException $e) {
            return $this;
        }
        $this->mFrom = $email;
        return $this;
    }

    /**
     * Set the content-Type of the Body-Document
     *
     * Leave empty to auto-detect:
     *
     * multipart/alternative:
     *  - Will show only HTML or TEXT Version
     *  - Last part is favorite
     *  - No attachments possible
     *
     * multipart/mixed
     *  - Will show any Version
     *  - Attachments possible
     *
     *
     * @param $contentType
     * @return $this
     */
    public function setContentType ($contentType=NULL) {
        $this->mContentType = strtolower($contentType);
        return $this;
    }

    public function getContentType () {
        return $this->mContentType;
    }

    public function setCharset ($charset="UTF-8") {
        $this->mCharset = $charset;
        return $this;
    }

    /**
     * Add a mail part
     *
     * @param MailPart $part
     * @return $this
     */
    public function addPart (MailPart $part) {
        $this->mParts[] = $part;
        return $this;
    }


    private function _getEMailString (array $objArr) {
        $ret = [];
        foreach ($objArr as $curObj) {
            $ret[] = $curObj->render();
        }
        return implode(", ", $ret);
    }

    private function _extendHeader (&$header, $headerName, $headerValue) {
        $eol = MailKernel::EOL;

        $headerValue = str_replace("\r\n", " ", $headerValue);
        $headerValue = str_replace("\n", " ", $headerValue);

        $header .= $headerName . ": " . $headerValue . $eol;
    }


    /**
     * Replace \r\n \n \r by \r\n
     *
     * So there will be no other line-feeds than \r\n
     *
     * @param $input
     * @return mixed
     */
    public function _cleanLfContent ($input) {
        $input = str_replace("\r\n", "\n", $input);
        $input = str_replace("\r", "\n", $input);
        $input = str_replace("\n", MailKernel::EOL, $input);
        return $input;
    }


    public function render (&$mailData=NULL) {
        $eol = MailKernel::EOL;

        $mailData = [];

        // Headers that are handled by mail()
        $optHeaders = "";

        if (count ($this->mTo) == 0) {
            throw new MailException("No recipient specified. You need to specify at least one recipient in the 'To:'-header");
        }
        if ($this->mSubject === NULL) {
            throw new MailException("No subject specified. You need to specify a 'Subject:' - header");
        }
        $this->_extendHeader($optHeaders, "Subject", $mailData["Subject"] = $this->mSubject);


        // Headers to be passed additionally to mail()
        $headers = "";
        if ($this->mFrom !== NULL) {
            $this->_extendHeader($headers, "From", $this->mFrom->render());
        }

        // Check Bypass for CC and BCC
        if (MailKernel::GetGlobalBypass() === FALSE) {
            $this->_extendHeader($optHeaders, "To", $mailData["To"] = $this->_getEMailString($this->mTo));

            if (count($this->mBcc) > 0) {
                $this->_extendHeader($headers, "Bcc", $this->_getEMailString($this->mBcc));
            }

            if (count($this->mCc) > 0) {
                $this->_extendHeader($headers, "Cc", $this->_getEMailString($this->mCc));
            }

        } else {
            $this->_extendHeader($headers, "X-GlobalBypass-To", MailKernel::GetGlobalBypass());
            $mailData["To"] = MailKernel::GetGlobalBypass();

            $this->_extendHeader($optHeaders, "X-Bypass-Orig-To", $this->_getEMailString($this->mTo));
            if (count($this->mBcc) > 0) {
                $this->_extendHeader($headers, "X-Bypass-Orig-Bcc", $this->_getEMailString($this->mBcc));
            }

            if (count($this->mCc) > 0) {
                $this->_extendHeader($headers, "X-Bypass-Orig-Cc", $this->_getEMailString($this->mCc));
            }
        }

        foreach ($this->mHeader as $key => $val) {
            $this->_extendHeader($headers, $key, $val);
        }

        if (count ($this->mParts) == 0) {
            throw new MailException("No mail part specified. You need to add at least one MailPart.");
        }

        if (count ($this->mParts) == 1 && ($this->mContentType === NULL || in_array(strtolower($this->mContentType), ["text/plain", "text/html"]))) {

            // Only one part - and no ContentType specified -> build single part Mail
            $partData = $this->mParts[0]->__getIntData();

            $contentType = $partData["contentType"];
            $charset = $partData["charset"];
            $contentTransferEncoding = $partData["contentTransferEncoding"];
            $content = $partData["content"];
            $this->_extendHeader($headers, "Content-type", "$contentType; charset=$charset");
            $this->_extendHeader($headers, "Content-transfer-encoding", $contentTransferEncoding);

            $mailData["headers"] = $headers;
            $mailData["content"] = $this->_cleanLfContent($content);

            $mail = $headers . $optHeaders . $eol;
            $mail .= $content;
            $this->mMailData = $mailData;
            return $this->_cleanLfContent($mail);
        }

        // We have a multipart mail
        $this->_extendHeader($headers, "MIME-Version", "1.0");

        $contentType = $this->mContentType;
        if ($contentType === NULL) {
            $contentType = "multipart/mixed";
        }

        $this->_extendHeader($headers, "Content-Type", "$contentType; boundary=\"{$this->__getBoundary()}\"");
        $mailData["headers"] = $headers;

        $content = "";
        foreach ($this->mParts as $curPart) {
            $content .= $this->_cleanLfContent($curPart->render($this));
        }
        $content .= "--" . $this->__getBoundary() . "--";
        $mailData["content"] = $this->_cleanLfContent($content);

        $this->mMailData = $mailData;
        return $headers . $optHeaders . $eol . $content;
    }

    /**
     * Send the E-Mail using the configured DeliveryAgent in MailKernel
     *
     * Shortcut for
     *
     * <code>
     * MailKernel::GetMailDeliveryAgent()->send($mailBody);
     * </code>
     *
     */
    public function send() {
        MailKernel::GetMailDeliveryAgent()->send($this);
    }

    /**
     * @return bool|mixed
     */
    public function getRenderedMailText() {
        if ( ! isset($this->mMailData["content"])) {
            return FALSE;
        }
        return $this->mMailData["content"];
    }

}
