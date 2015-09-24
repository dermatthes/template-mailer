<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 24.09.15
 * Time: 13:03
 */



namespace de\leuffen\template_mailer;


class FileAttachment extends MailPart {


    public function __construct ($fileName) {
        parent::__construct(
            chunk_split(
                base64_encode(
                    file_get_contents($fileName)
                ),
                75
            ),
            MIME::ByFileName($fileName),
            "UTF-8",
            "base64");
    }


}