<?php

namespace qui\file_worker;


use qui\App;

/**
 * Class CacheHelper
 * @package qui\file_worker
 */
interface CacheInterface
{
    /**
     * @param string $key
     * @param mixed $cachingData
     * @param null $group caching key group name to keep data separate.
     * @param int|null $expiration how long shall cache live
     *
     * @return bool operation status
     */
    public static function setKey($key = '', $cachingData = '', $group = null, $expiration = null);
    
    
    /**
     * @param string $key
     * @param null $group if needed cache from specified group
     * @return mixed
     */
    public static function getKey($key = '', $group = null);
}
