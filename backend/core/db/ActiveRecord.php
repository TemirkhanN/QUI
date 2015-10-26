<?php

namespace core\db;
use core\app\AppLog;


/**
 * Class ActiveRecord
 * @package \core\db
 *
 * This class is my view on active record. I'm not really sure if it is AR at all
 *
 * Tis parent class, so AR shall extend it. nothing more
 *
 * For example: you have table named favourites with next columns
 * id | target | target_id | owner_id
 *
 * 5    article    140          3
 *
 *
 * you create new class at /backend/models/database/Favourites.php with next contain
 *
 *
 *  namespace app\models\database;
 *
 *  use core\db\ActiveRecord;
 *
 *  class Favourites extends ActiveRecord
 *  {
 *  }
 *
 * now you're be able to use it anywhere like so
 *
 * $newFavourite = new Favourites();
 *
 * $newFavourite->target = 'photo';
 * $newFavourite->targetId = 15;
 * $newFavourite->owner_id = 1;
 * $newFavourite->save();
 *
 * This will insert into table new row with declared values
 *
 *
 * next one will get existing row
 *
 * $someFavourite = new Favourite(5);
 *
 * echo $someFavourite->target; //Will print "article"
 * $someFavourite->target = 'content';
 * $someFavourite->owner_id = 4;
 * $someFavourite->save(); //Will update existing record with id 5 to new declared values
 *
 */
class ActiveRecord implements ActiveRecordInterface
{

    const OBJ = 'object';
    const ARR = 'array';


    /**
     * @var DBMSOperator|bool
     */
    private $_db;

    // This variable always declared. Contains primary key of record
    public $id;

    // Name of table that record extends to declare structure and save/delete/update & etc operations
    private $_tableName;


    /**
     * Detects class name from which context method has been called
     * and passes its name as table to new instance of active record
     *
     * @return ActiveRecord
     */
    private static function init()
    {
        $tableName = get_called_class();

        $tableName = explode('\\', $tableName);
        $tableName = array_pop($tableName);

        $activeRecord = new self($tableName);

        return $activeRecord;
    }


    /**
     * Creates new active record
     * @param string | null $realTableName forcely sets ActiveRecords table to choosen
     * It tries to get element from table with such identifier
     */
    public function __construct($realTableName = null)
    {

        try {
            $this->_db = \App::$app->db();

            if (!$this->_db instanceof DBMSOperator) {
                throw new \ErrorException('Connection to database is not set or not instance of "Connection"');
            } else {
                $this->_tableName = $realTableName ? $realTableName : lcfirst((new \ReflectionClass($this))->getShortName());
                $this->_tableName = preg_replace_callback('#(?![A-Z]).[A-Z]#', function($match){
                    $chars = str_split($match[0]);
                    return strtolower(implode('_', $chars));
                }, $this->_tableName);
                $this->_tableName = strtolower($this->_tableName);
            }

        } catch (\ErrorException $e) {
            AppLog::noteError($e);
            return;
        }
    }


    /**
     * Gets one record from table satisfying passed condition
     *
     * !!!NOTE: method returns ONLY array or false. Mixed is added to hide
     * "property not found in class" warnings in IDE
     *
     * @param string $where conditions that passed as ['column'=>'columnValue', ...]
     * @return array | bool | mixed AR fields and data as assoc array if it exists
     */
    public static function findOne($where)
    {
        $activeRecord = self::init();

        $data = $activeRecord->_db
            ->get()
            ->from($activeRecord->_tableName)
            ->where($where)
            ->limit(1)
            ->launch();

        if (!$data) {
            return false;
        }

        $activeRecord->declareFields($data[0]);

        return $activeRecord;
    }


    /**
     * Returns all elements from AR table satisfying condition
     *
     * Null condition means all rows
     *
     *
     * @param string | null $where condition to get items from database
     * @return false | array items found by condition passed
     */
    public static function findAll($where = null)
    {

        $activeRecord = self::init();

        $data = $activeRecord->_db
            ->get()
            ->from($activeRecord->_tableName)
            ->where($where)
            ->launch();

        return $data ? $data : false;
    }


    /**
     * @param string $where condition
     * @return mixed
     */
    public static function deleteOne($where)
    {

        $activeRecord = self::init();

        return $activeRecord->_db
            ->delete()
            ->from($activeRecord->_tableName)
            ->where($where)
            ->limit(1)
            ->launch();
    }


    /**
     * @param string $where condition
     * @return mixed
     */
    public static function deleteAll($where)
    {
        $activeRecord = self::init();

        return $activeRecord->_db
            ->delete()
            ->from($activeRecord->_tableName)
            ->where($where)
            ->launch();
    }


    /**
     * Tries to save containing data to database
     * It is necessary for record to have id key as long as it is mostly used as primary key
     * If record has id, method will update row otherwise new row will be inserted
     * @return bool | int if AR data save is successful / item id if new record created
     */
    public function save()
    {
        $activeRecord = $this->getPublicProperties();

        if ($activeRecord['id']) {
            if (
            $this->_db
                ->update($activeRecord)
                ->to($this->_tableName)
                ->where(['id' => $activeRecord['id']])
                ->launch()
            ) {
                return true;
            }
        } else {
            if ($this->_db->add($activeRecord)->to($this->_tableName)->launch()) {
                return (int)$this->_db->lastInsertId();
            }
        }

        return false;
    }


    /**
     * @return array $properties
     */
    private function getPublicProperties()
    {
        $publicProperties = (new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PUBLIC);

        $properties = [];
        foreach ($publicProperties as $property) {
            $properties[$property->name] = $this->{$property->name};
        }

        return $properties;
    }


    /**
     * @param array $fields AR's columns and their values values
     */
    protected function declareFields($fields)
    {
        foreach ($fields as $field => $value) {
            $this->{$field} = $value;
        }
    }

}