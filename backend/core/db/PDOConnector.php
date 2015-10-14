<?php

namespace core\db;


/**
 * Class PdoConnector
 * @package core\db
 *
 * Mother of all database actions. Tis some kind of cover of getting, checking, deleting and updating queries
 * Comments are in russian. It will be changed soon
 */
class PdoConnector
{


    /**
     * @param string $dsn name of database management system
     * @param array $config
     * @return DBMSOperator || false
     */
    protected static function connect($dsn, $config)
    {
        $connectTo = 'connect' . ucfirst($dsn);

        if (method_exists(get_class(), $connectTo)) {
            $connection = static::$connectTo($config);
            $dbOperator = static::getDsnClass($dsn);
            return new $dbOperator($connection);
        }

        return false;
    }


    /**
     * @param array $config
     * @return \PDO
     */
    private static function connectMySql($config)
    {
        return new \PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['name'] . ';charset=utf8', $config['user'], $config['password']);
    }


    /**
     * @param array $config
     * @return \PDO
     */
    private static function connectPgsql($config)
    {
        return new \PDO('pgsql:host=' . $config['host'] . ';port=5432;dbname=' . $config['name'] . ';charset=utf8', $config['user'], $config['password']);
    }


    /**
     * @param array $config
     * @return \PDO
     */
    private function connectMssql($config)
    {
    }


    /**
     * @param array $config
     * @return \PDO
     */
    private function connectSqlite($config)
    {
    }


    /**
     * @param string $dsn name of database source(mysql, pgsql, mssql, sqlite)
     * @return string class name of dsn operator
     */
    private static function getDsnClass($dsn)
    {
        return __NAMESPACE__ . '\\' . $dsn . '\\' . ucfirst($dsn) . 'Operator';
    }

} 