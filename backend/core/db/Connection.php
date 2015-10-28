<?php

namespace core\db;

use core\App;
use core\app\AppLog;

/**
 * Class Connection
 * @package core\db
 */
class Connection extends PDOConnector
{
    protected static $connections = [];


    /**
     * @param string $dsn name of database management system
     * @param array $config
     * @return DBMSOperator || false
     */
    public static function open($dsn, $config)
    {
        $connectionName = $dsn . '_' . $config['name'];
        if (!static::opened($connectionName)) {
            try {
                self::$connections[$connectionName] = self::connect($dsn, $config);
            } catch (\PDOException $e) {
                AppLog::noteError($e);
                return false;
            }
        }

        return self::$connections[$connectionName];
    }


    /**
     * @param string $connectionName
     */
    public static function close($connectionName)
    {
        unset(self::$connections[$connectionName]);
    }



    /**
     * @param string $connectionName
     * @return bool
     */
    public static function opened($connectionName)
    {
        return isset(self::$connections[$connectionName]);
    }

}