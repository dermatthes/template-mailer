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

}