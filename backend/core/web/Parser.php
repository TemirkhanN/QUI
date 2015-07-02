<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 23.05.2015
 * Time: 20:23
 */

namespace app\core\web;


class Parser {



    //Находит и возвращает ссылки на все изображения в контенте
    public static function matchPics($content){
        preg_match_all('!img src="([a-z0-9_\.\/]+\.(?:jpe?g|png|gif))!Ui' , $content , $matches);

        return $matches[1];
    }
###function end###

}