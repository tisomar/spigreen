<?php

namespace QPress\Mailing;

use QPress\Template\Template;

class TemplateMailing extends Template {

     public function __construct($file)
     {
         self::$path = __DIR__ . '/Resources/views/';
         parent::__construct($file);
     } 
    
    public function style($selector) {

        $rgb = '#999999';
        if (preg_match('/#[0-9A-Za-z]{6}/', \Config::get('mail_rgb'))) {
            $rgb = \Config::get('mail_rgb');
        }

        $style = array(
            
            'color-principal' => $rgb,
            
            'background-site' => 'background-color: #E8E8E8;',
            'background_body' => 'E8E8E8',

            'font-header' => 'color: #fefefe; font-size: 11px; font-weight: normal; font-family: Arial, Helvetica, sans-serif;',
            
            // --------------------------------------------------------
            
            'h2' =>  'color: #333333; '
                    . 'font-size: 16px; '
                    . 'font-weight: bold; '
                    . 'font-family: Helvetica, Arial, sans-serif; '
                    . 'letter-spacing: 2px; '
                    . 'text-transform: uppercase; '
                    . 'margin-bottom: 30px; '
                    . 'text-align: center; '
            
            , 'h4' => 'color: #484848; '
                    . 'font-size: 13px; '
                    . 'font-weight: bold; '
                    . 'font-family: Helvetica, Arial, sans-serif;'
                    . 'letter-spacing: 1px; '
                    . 'margin-bottom: 15px; '
            
            , 'hr' => 'border: 1px solid #E8E8E8; '
                    . 'height: 0px; '
                    . 'margin-top: 10px; '
                    . 'margin-bottom: 10px;'
            
            , 'p' => 'color: #636363; '
                    . 'font-size: 12px; '
                    . 'font-weight: normal; '
                    . 'line-height: 20px; '
                    . 'font-family: Helvetica, Arial, sans-serif;'
            
            , 'td' => 'color: #636363; '
                    . 'font-size: 12px; '
                    . 'font-weight: normal; '
                    . 'line-height: 20px; '
                    . 'font-family: Helvetica, Arial, sans-serif;'
                    . 'padding: 10px 0px; '
                    . 'border-top: 1px solid #E8E8E8;'
            ,
            
        );
        
        return isset($style[$selector]) ? $style[$selector] : '';
        
    }
    
}