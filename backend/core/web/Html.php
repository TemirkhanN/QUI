<?php
/**
 * Created by PhpStorm.
 * User: temirkhan
 * Date: 20.04.15
 * Time: 23:12
 */

namespace app\core\web;


class Html {



    public static function encode($string)
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }



    public static function decode($string)
    {
        return htmlspecialchars_decode($string, ENT_QUOTES);
    }



} 