<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 21.07.15
 * Time: 13:04
 */


namespace Leuffen\TemplateMailer;



use Leuffen\TemplateMailer\Exception\InvalidEMailAddressException;

class EMailAddress {

    private $mName = NULL;
    private $mAddr = NULL;

    public function __construct ($value) {
        $value = trim ($value);
        if (preg_match ('/^([\"\']?)(.*?)\1 <([a-z0-9\.\-\_\@]+)>$/i', $value, $matches)) {
            $this->mName = $matches[2];
            $this->mAddr = $matches[3];
            return;
        }
        if (preg_match ('/^<([a-z0-9\.\-\_\@]+)>$/i', $value, $matches)) {
            $this->mAddr = $matches[1];
            return;
        }
        if (preg_match ('/^([a-z0-9\.\-\_\@]+)$/i', $value, $matches)) {
            $this->mAddr = $matches[1];
            return;
        }
        throw new InvalidEMailAddressException("'$value' is no valid eMail Address.");
    }




    public function render() {
        if ($this->mName !== NULL)
            return "{$this->mName} <{$this->mAddr}>";
        return "{$this->mAddr}";
    }

}