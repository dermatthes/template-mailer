<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 24.09.15
 * Time: 13:27
 */

    namespace Leuffen\TemplateMailer;


    class MIME {


        private static $sMimeTypes = [
            'txt' 	=> 'text/plain',
            'html' 	=> 'text/html',
            'htm'	=> 'text/html',
            'php' 	=> 'text/plain',
            'css' 	=> 'text/css',
            'js'	=> 'application/x-javascript',
            'jpg' 	=> 'image/jpeg',
            'jpeg' 	=> 'image/jpeg',
            'gif' 	=> 'image/gif',
            'png' 	=> 'image/png',
            'bmp' 	=> 'image/bmp',
            'tif' 	=> 'image/tiff',
            'tiff'	=> 'image/tiff',
            'doc' 	=> 'application/msword',
            'docx'	=> 'application/msword',
            'xls' 	=> 'application/excel',
            'xlsx'	=> 'application/excel',
            'ppt' 	=> 'application/powerpoint',
            'pptx' 	=> 'application/powerpoint',
            'pdf'	=> 'application/pdf',
            'wmv' 	=> 'application/octet-stream',
            'mpg' 	=> 'video/mpeg',
            'mov' 	=> 'video/quicktime',
            'mp4' 	=> 'video/quicktime',
            'zip' 	=> 'application/zip',
            'rar' 	=> 'application/x-rar-compressed',
            'dmg' 	=> 'application/x-apple-diskimage',
            'exe'	=> 'application/octet-stream'
        ];


        /**
         * Return the Content-Type by File-Extension.
         *
         * @param $ext
         * @return string
         */
        public static function ByExtension ($ext) {
            if ( ! isset (self::$sMimeTypes[$ext]))
                return "application/octet-stream";
            return self::$sMimeTypes[$ext];
        }

        /**
         * Return the MIME Type for a file/pathname
         *
         * @param $fileName
         * @return string
         */
        public static function ByFileName ($fileName) {
            $ext = explode (".", strtolower($fileName));
            if (count ($ext) == 0)
                return "application/octet-stream";
            return self::ByExtension($ext[count($ext)-1]);
        }
    }