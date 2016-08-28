<?php

namespace qui\helper;


class StringHelper
{

    public static function toCamelCase($string = '')
    {
        $string = preg_replace_callback('#-(\w)#i', function($match){
            return ucfirst($match[1]);
        }, $string);

        return ucfirst($string);
    }
}