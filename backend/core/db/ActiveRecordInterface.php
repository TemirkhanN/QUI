<?php


namespace core\db;

/**
 * Interface ActiveRecordInterface
 * @package core\db
 */
interface ActiveRecordInterface
{

    public static function findOne($condition);


    public static function findAll($condition);


    public function save();

}