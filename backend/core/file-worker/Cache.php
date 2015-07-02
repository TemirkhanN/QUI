<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 28.06.2015
 * Time: 13:44
 */

namespace app\core\file\worker;


use app\core\App;




class Cache
{


    public static  $cacheDir = '/cache';

    /**
     * @param string $fileName
     * @param string $cachingData
     * @param null $dir folder in cache dir if needed
     * @return bool|string returns false if error occurred or cache file absolute path if success.
     */
    public static function saveCache($fileName = '', $cachingData = '', $dir = null)
    {

        $dir = $dir!=null ? str_replace('..', '', $dir) : null;
        $dir = $dir != null ? ROOT_DIR . self::$cacheDir . '/content/' . $dir . '' : self::$cacheDir . '/content';

        $destination = $dir . '/' . $fileName . '.so';

        if (!is_dir($dir)){
            mkdir($dir, 0775, true);
        }

        if (file_put_contents($destination, $cachingData)){
            return $destination;
        }

        return false;

    }




    private static function cacheExpired($path = '')
    {

        $cacheSettings = App::getConfig('caching');


        if(filemtime($path) > time() - $cacheSettings['cacheTime']){
            return true;
        }

        return false;
    }


    /**
     * @param string $fileName
     * @param null $dir if needed cache from specified folder
     * @return bool|string returns cacheFile if succeed or false if error occurred.
     */
    public static function getCache($fileName = '', $dir = null)
    {

        $dir = $dir!=null ? str_replace('..', '', $dir): null;
        $dir = $dir != null ? ROOT_DIR .self::$cacheDir . '/content/' . $dir . '' : self::$cacheDir . '/content';

        $cacheFile = $dir . '/' . $fileName . '.so';

        if(file_exists($cacheFile) && !self::cacheExpired($cacheFile)){
            return file_get_contents($cacheFile);
        } else{
            return false;
        }
    }

}