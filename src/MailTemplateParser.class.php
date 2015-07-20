<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 7/20/15
 * Time: 9:25 PM
 */

namespace de\leuffen\template_mailer;

class MailTemplateParser {


    public function loadTemplate ($template) {

    }

    /**
     * @param $data
     * @return MailBody
     */
    public function apply ($data) {

    }

    /**
     * Shortcut for
     *
     * MailTemplateParser->apply()->send()
     *
     * @param $data
     * @param string $mtaAlias
     * @return MailBody
     */
    public function send ($data, $mtaAlias="default") {

    }


}
