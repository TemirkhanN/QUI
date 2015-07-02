<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 11.06.2015
 * Time: 2:26
 */

namespace app\plugins\translit;


use app\core\base\Plugin;

class Translit implements Plugin
{

    private static $consonant = [];



    public static function init()
    {
        self::$consonant = [
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
        ];
    }



    /* Changes russian chars to english chars that consonant them
     * Mostly this function is needed for only russian websites
     */
    public static function translit($string = '')
    {

        return strtr($string, self::$consonant);

    }

}