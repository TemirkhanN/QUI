<?php

namespace qui\file_worker;
use qui\app\AppLog;


/**
 * Class FileCacheHelper
 * @package qui\file_worker
 */
class FileCacheHelper implements CacheInterface
{

    /**
     * @var string
     */
    private static $generalCacheDir = '/cache/general'; // Controllable cache folder


    /**
     * @param string $key
     * @param string $cachingData
     * @param null $group
     * @param null $expiration has no effect
     * @return bool
     */
    public static function setKey($key = '', $cachingData = '', $group = null, $expiration = null)
    {
        $dir = self::$generalCacheDir;

        if ($group === null) {
            $dir = ROOT_DIR . self::$generalCacheDir . '/' . str_replace('.', '', $group);
        }

        $destination = $dir . '/' . $key . '.so';

        if (!@mkdir($dir, 0775, true) && !is_dir($dir)) {
            return false;
        }

        if (file_put_contents($destination, $cachingData)) {
            return true;
        }

        return false;

    }

    /**
     *
     * @param string $filePath full path to file that shall be ckecked
     * if its doesn't exist or last modify time greeter than cache time defined in settings(/config/main.php)
     * @return bool
     *
     * @param string $filePath
     * @return bool
     * @throws \LogicException
     */
    protected static function expired($filePath = '')
    {
        /**
         * @var array $cacheSettings
         */
        $cacheSettings = \Qui::getConfig('caching');

        if (!is_array($cacheSettings)) {
            throw new \LogicException('Cache settings shall be set before usage');
        }
        if (!file_exists($filePath) || filemtime($filePath) < time() - $cacheSettings['cacheTime']) {
            return true;
        }

        return false;
    }


    /**
     * @param string $key
     * @param null $group
     * @return bool|string
     */
    public static function getKey($key = '', $group = null)
    {
        $dir = self::$generalCacheDir;
        if ($group === null) {
            $dir = ROOT_DIR . self::$generalCacheDir . '/' . str_replace('.', '', $group);
        }

        $cacheFile = $dir . '/' . $key . '.so';

        try {
            if (!self::expired($cacheFile)) {
                return file_get_contents($cacheFile);
            }
        } catch(\LogicException $e){
            AppLog::noteError($e);
        }

        return false;
    }
}