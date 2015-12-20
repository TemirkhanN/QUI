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
     * @var DBMSOperator
     */
    private $db;

    // Name of table that record extends to declare structure and save/delete/update & etc operations
    private $tableName;


    private $fields = [];


    /**
     * Detects class name from which context method has been called
     * and passes its name as table to new instance of active record
     *
     * @return ActiveRecord
     */
    private static function init()
    {
        $tableName = get_called_class();

        $activeRecord = new self($tableName);

        return $activeRecord;
    }


    public function __get($column)
    {
        return isset($this->fields[$column]) ? $this->fields[$column] : null;
    }


    public function __set($column, $value)
    {
        $this->fields[$column] = $value;
    }


    /**
     * Creates new active record
     * @param string | null $realTableName forcely sets ActiveRecords table to choosen
     * It tries to get element from table with such identifier
     */
    public function __construct($realTableName = null)
    {
        try {
            $this->db = \App::$app->db();

            if (!$this->db instanceof DBMSOperator) {
                throw new \ErrorException('Connection to database is not set or not instance of "Connection"');
            } else {
                $this->tableName = $realTableName ? $realTableName : lcfirst((new \ReflectionClass($this))->getShortName());
                $this->tableName = basename($this->tableName);
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
     * @param array|null $where conditions that passed as ['column'=>'columnValue', ...]
     * @return ActiveRecord|false AR fields and data as assoc array if it exists
     */
    public static function findOne($where = null)
    {
        $activeRecord = self::init();

        $data = $activeRecord->db
            ->get()
            ->from($activeRecord->tableName)
            ->where($where)
            ->limit(1)
            ->launch();

        if (!$data) {
            return false;
        }

        $activeRecord = $activeRecord->declareFields($data[0]);

        return $activeRecord;
    }


    /**
     * Returns all elements from AR table satisfying condition
     *
     *
     *
     * @param array|null $where condition to get items from database
     * @param int $offset
     * @param int $limit
     * @return array|false items found by condition passed
     */
    public static function findAll($where = null, $offset = 0, $limit = 0)
    {
        $activeRecord = self::init();

        $data = $activeRecord->db
            ->get()
            ->from($activeRecord->tableName)
            ->where($where)
            ->offset($offset)
            ->limit($limit)
            ->launch();

        if ($data) {
            foreach ($data as $key => $item) {
                $data[$key] = $activeRecord->declareFields($item);
            }
            return $data;
        }

        return false;
    }


    /**
     * @param string $where condition
     * @return mixed
     */
    public static function deleteOne($where)
    {
        $activeRecord = self::init();

        return $activeRecord->db
            ->delete()
            ->from($activeRecord->tableName)
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

        return $activeRecord->db
            ->delete()
            ->from($activeRecord->tableName)
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
        if ($this->fields['id']) {
            if (
            $this->db
                ->update($this->fields)
                ->to($this->tableName)
                ->where(['id', '=', $this->fields['id']])
                ->launch()
            ) {
                return true;
            }
        } else {
            if ($this->db->add($this->fields)->to($this->tableName)->launch()) {
                return (int)$this->db->lastInsertId();
            }
        }

        return false;
    }

    /**
     * @param array $fields AR's columns and their values values
     * @return ActiveRecord
     */
    protected function declareFields($fields)
    {
        $activeRecord = clone $this;
        foreach ($fields as $field => $value) {
            $activeRecord->{$field} = $value;
        }

        return $activeRecord;
    }

}