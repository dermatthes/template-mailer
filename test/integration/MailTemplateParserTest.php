<?php
use de\leuffen\template_mailer\MailTemplateParser;
use de\leuffen\text_template\TextTemplate;

/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 17.07.15
 * Time: 15:55
 */


    class MailTemplateParserTest extends PHPUnit_Framework_TestCase {




        public function testAllResultsMatchExpectedResult () {
            $dirs = glob(__DIR__ . "/tpls/*");
            $tt = new MailTemplateParser();
            $tt->__setFixedBoundary();

            foreach ($dirs as $dir) {
                echo "\nTesting $dir...";
                $tt->loadTemplate(file_get_contents($dir . "/_in.txt"));
                $data = require ($dir . "/_in.php");
                $mail = $tt->apply($data);
                $this->assertEquals(str_replace("\n", "\r\n", file_get_contents($dir . "/out.txt")), $mail->render(), "Error in check: {$dir}");
            }

        }

    }