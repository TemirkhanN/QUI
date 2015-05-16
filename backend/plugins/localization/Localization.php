<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 16.05.2015
 * Time: 1:34
 */

namespace app\plugins\Localization;


use app\core\App;
use app\core\base\Plugin;

class Localization implements Plugin{

    const LANG_ID = 'en';
    private static $dictionary;
    private static $langs;




    public static function init()
    {
        self::loadDictionaries();
    }



    public static function loadDictionary($langId = self::LANG_ID)
    {
        try {
            if (is_dir(__DIR__ . '/lang/' . $langId)) {
                if (file_exists(__DIR__ . '/lang/' . $langId . '/dic.php')) {
                    self::$dictionary[$langId] = App::requireFile(__DIR__ . '/lang/' . $langId . '/dic.php');
                } else{
                    throw new \Exception("Language dictionary-file doesn't exist in directory");
                }
            } else{
                throw new \Exception('Language '. $langId . ' not found in directory');
            }
        } catch(\Exception $error) {
            App::noteError($error);
        }
    }




    public static function loadDictionaries()
    {
        self::getLangList();

        if(self::$langs){
            foreach(self::$langs as $langId){
                self::loadDictionary($langId);
            }
        }
    }




    public static function getLangList()
    {
        self::$langs = App::requireFile(__DIR__ . '/lang/lang-list.php');
    }



    public static function translate($text = '', $langId = 'en')
    {


        return isset(self::$dictionary[$langId][$text]) ? self::$dictionary[$langId][$text] : $text;
    }

}


Localization::init();