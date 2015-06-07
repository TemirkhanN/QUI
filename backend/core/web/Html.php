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




    //Функция для транслита
    public static function translit($string) {

        $converter = array(
            'а' => 'a',	'б' => 'b',	'в' => 'v', 'г' => 'g',
            'д' => 'd',	'е' => 'e',	'ё' => 'e',	'ж' => 'zh',
            'з' => 'z',	'и' => 'i',	'й' => 'y',	'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts',
            'ч' =>'ch', 'ш' =>'sh', 'щ'=>'sch', 'ь' => '',
            'ы' => 'y', 'ъ' => '',  'э' => 'e', 'ю' => 'yu',
            'я' => 'ya','А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'E',
            'Ж' => 'Zh','З' => 'Z', 'И' => 'I', 'Й' => 'Y',
            'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S',
            'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H',
            'Ц' => 'Ts','Ч' => 'Ch','Ш' => 'Sh','Щ' => 'Sch',
            'Ь' => '_',  'Ы' => 'Y', 'Ъ' => '',  'Э' => 'E',
            'Ю' => 'Yu','Я' => 'Ya', ' '=>'_', '"'=>'',
            '\''=>'',   '?'=>'', '!'=>'', ','=>'', '.'=>'',
            '-'=>'', '+'=>'','='=>''
        );


        return strtr($string, $converter);

    }
    ###function end###

} 