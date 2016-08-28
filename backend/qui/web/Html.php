<?php

namespace qui\web;


class Html
{
    public static function encode($string)
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function decode($string)
    {
        return htmlspecialchars_decode($string, ENT_QUOTES);
    }
} 