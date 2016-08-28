<?php


namespace qui\db;

/**
 * Interface ActiveRecordInterface
 * @package core\db
 */
interface ActiveRecordInterface
{

    public function __set($field, $value);
    
    public function __isset($field);

    public function __get($field);

    public static function findOne($condition);


    public static function findAll($condition);


    public function save();

}