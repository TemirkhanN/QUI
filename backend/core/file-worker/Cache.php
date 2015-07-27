<?php
/**
 * Basic rigidbody for caching system
 * Tis for future for easy integration if memcache, redis or kind'a that will be used in project.
 */

namespace app\core\file_worker;


use app\core\App;


class Cache
{

    private static $generalCacheDir = '/cache/general'; // Controllable cache folder
    private static $systemCacheDir = '/cache/sys'; //System cache folder. For future

    /**
     * @param string $key
     * @param string $cachingData
     * @param null $group caching key group name to keep data separate.
     * For example setKey('rating', 100, 'users');
     * Also it shall not contain points(they will be replaced for security reasons)
     *
     * @return bool information about operation success
     */
    public static function setKey($key = '', $cachingData = '', $group = null)
    {

        $group = $group != null ? str_replace('.', '', $group) : null;
        $dir = $group != null ? ROOT_DIR . self::$generalCacheDir . '/' . $group . '' : self::$generalCacheDir;

        $destination = $dir . '/' . $key . '.so';

        if (!is_dir($dir)){
            mkdir($dir, 0775, true);
        }

        if (file_put_contents($destination, $cachingData)){
            return true;
        }

        return false;

    }


    /**
     * @param string $filePath full path to file that shall be ckecked
     * if its doesn't exist or last modify time greeter than cache time defined in settings(/config/main.php)
     * @return bool
     */
    private static function expired($filePath = '')
    {

        $cacheSettings = App::getConfig('caching');

        if(!file_exists($filePath) || filemtime($filePath) < time() - $cacheSettings['cacheTime']){
            return true;
        }

        return false;
    }


    /**
     * @param string $key
     * @param null $group if needed cache from specified group
     * @return bool|string returns cacheFile if succeed or false if error occurred.
     */
    public static function getKey($key = '', $group = null)
    {

        $group = $group!=null ? str_replace('.', '', $group): null;
        $group = $group != null ? ROOT_DIR . self::$generalCacheDir . '/' . $group . '' : self::$generalCacheDir;

        $cacheFile = $group . '/' . $key . '.so';

        if(!self::expired($cacheFile)){
            return file_get_contents($cacheFile);
        }

        return false;

    }

}
