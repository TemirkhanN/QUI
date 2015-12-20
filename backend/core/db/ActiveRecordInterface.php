<?php


namespace core\db;

/**
 * Interface ActiveRecordInterface
 * @package core\db
 */
interface ActiveRecordInterface
{

    public function __set($column, $value);

    public function __get($column);

    public static function findOne($condition);


    public static function findAll($condition);


    public function save();

}