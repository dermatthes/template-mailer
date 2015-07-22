<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 7/20/15
 * Time: 9:23 PM
 */


namespace de\leuffen\template_mailer;


class MailPart {


    private $mContentTransferEncoding = "8bit";
    private $mCharset = "UTF-8";
    private $mContentType = "text/plain";
    private $mContentDisposition = NULL;
    private $mContentDispositionFileName = NULL;
    private $mContentDispositionToken = NULL;
    private $mContent = "";


    public function __construct ($content="", $contentType="text/plain", $charset="UTF-8", $contentTransferEncoding="quoted-printable") {
        $this->mContent = $content;
        $this->mContentType = $contentType;
        $this->mCharset = $charset;
        $this->mContentTransferEncoding = $contentTransferEncoding;
    }



    /**
     * Set the charset of this MessagePart
     *
     * Default: UTF-8
     *
     * @param string $charset
     * @return $this
     */
    public function setCharset ($charset="UTF-8") {
        $this->mCharset = $charset;
        return $this;
    }

    /**
     * @param string $contentTransferEncoding
     * @return $this
     */
    public function setContentTransferEncoding ($contentTransferEncoding="quoted-printable") {
        $this->mContentTransferEncoding = $contentTransferEncoding;
        return $this;
    }

    /**
     * @param $contentType
     * @return $this
     */
    public function setContentType ($contentType) {
        $this->mContentType = $contentType;
        return $this;
    }

    /**
     * Set the content of this part
     *
     * the content should be correctly encoded
     *
     * Use MailContentTransferEncoder to encode the content properly.
     *
     * @param $content
     * @return $this
     */
    public function setContent ($content) {
        $this->mContent = $content;
        return $this;
    }

    /**
     * Set the content-disposition.
     *
     * Param1: type
     *  inline:         Show in Editor
     *  attachement:    Add as Attachement
     *
     *
     * @param string $type
     * @return $this
     */
    public function setContentDisposition ($type="attachment") {
        $this->mContentDisposition = $type;
        return $this;
    }


    /**
     * @param $fileName
     * @return $this
     */
    public function setContentDispositionFileName ($fileName) {
        if ($this->mContentDisposition === NULL)
            $this->mContentDisposition = "attachment";
        $this->mContentDispositionFileName = $fileName;
        return $this;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setContentDispositionToken ($token) {
        if ($this->mContentDisposition === NULL)
            $this->mContentDisposition = "attachment";
        $this->mContentDispositionToken = $token;
        return $this;
    }


    /**
     * Used by MailBody to access the relevant data in case of only one MailPart was added.
     *
     * @return array
     */
    public function __getIntData () {
        return ["content"=>$this->mContent, "contentType"=>$this->mContentType, "charset"=>$this->mCharset, "contentTransferEncoding"=>$this->mContentTransferEncoding];
    }

    private function _extendHeader (&$header, $headerName, $headerValue) {
        $eol = MailKernel::EOL;

        $headerValue = str_replace("\r\n", " ", $headerValue);
        $headerValue = str_replace("\n", " ", $headerValue);
        $headerValue = str_replace("\r", " ", $headerValue);

        $header .= $headerName . ": " . $headerValue . $eol;
    }


    public function render (MailBody $body) {
        $eol = MailKernel::EOL;

        $headers = "--" . $body->__getBoundary() . $eol;

        $this->_extendHeader($headers, "Content-Type", "{$this->mContentType}; charset={$this->mCharset}");
        $this->_extendHeader($headers, "Content-transfer-encoding", $this->mContentTransferEncoding);
        if ($this->mContentDisposition !== NULL) {
            $dispo = $this->mContentDisposition;
            if ($this->mContentDispositionFileName !== NULL)
                $dispo .= "; filename=\"" . addslashes($this->mContentDispositionFileName) . "\"";
            if ($this->mContentDispositionToken !== NULL)
                $dispo .= "; token=\"". addslashes($this->mContentDispositionToken) . "\"";
            $this->_extendHeader($headers, "Content-Disposition", $dispo);
        }

        $mailPart = $headers . $eol;

        $mailPart .= $this->mContent . $eol;
        return $mailPart;
    }


}