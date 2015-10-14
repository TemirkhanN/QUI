<?php

namespace core\helper;


class String
{

    public static function toCamelCase($word = '')
    {
        while ($pos = strpos($word, '-')) {
            $word = mb_substr($word, 0, $pos) . ucfirst(substr($word, $pos + 1));
        }

        return ucfirst($word);
    }


}