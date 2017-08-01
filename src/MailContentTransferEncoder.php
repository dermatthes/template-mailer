<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 21.07.15
 * Time: 16:10
 */

namespace Leuffen\TemplateMailer;


class MailContentTransferEncoder {



    public function encodeQuotedPrintable ($input) {
        return wordwrap(quoted_printable_encode($input), 75, MailKernel::EOL);
    }

    public function encodeBase64 ($input) {
        return chunk_split(base64_encode($input), 76);
    }


    public function encode ($input, $targetContentTransferEncoding) {
        switch (strtoupper($targetContentTransferEncoding)) {
            case "QUOTED-PRINTABLE":
                return $this->encodeQuotedPrintable($input);
            case "BASE64":
                return $this->encodeBase64($input);
            case "8BIT":
                return  wordwrap($input, 900, MailKernel::EOL); // Already UTF-8;
            case "7BIT": {
                if ( ! function_exists("imap_utf7_encode"))
                    throw new \Exception("Cannot encode 7Bit Message: imap_utf7_encode() function missing. Please install the imap package.");
                return imap_utf7_encode(utf8_decode($input));
            }
        }
        throw new \InvalidArgumentException("No encoder available for targetContentTransferEncoding: '$targetContentTransferEncoding'");
    }

}