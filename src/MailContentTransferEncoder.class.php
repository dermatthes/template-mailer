<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 21.07.15
 * Time: 16:10
 */

namespace de\leuffen\template_mailer;


class MailContentTransferEncoder {



    public function encodeQuotedPrintable ($input) {
        return wordwrap(quoted_printable_encode($input), 75, "\r\n");
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
        }
        throw new \InvalidArgumentException("No encoder available for targetContentTransferEncoding: '$targetContentTransferEncoding'");
    }

}