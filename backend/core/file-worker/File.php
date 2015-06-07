<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 23.05.2015
 * Time: 20:26
 */

namespace app\core\file\worker;


class File {





    public static function deleteFile($file){
        if (file_exists($file)){
            unlink($file);
        }

        return;
    }
}