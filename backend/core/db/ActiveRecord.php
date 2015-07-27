<?php

namespace app\core\db;


use app\core\App;


/**
 * Class ActiveRecord
 * @package app\core\db
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
 *  use app\core\db\ActiveRecord;
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
class ActiveRecord{

    const OBJ = 'object';
    const ARR = 'array';

    public $id; // This variable always declared. Contains primary key of record
    private $_information = []; // Class information such as namespace, table name, table schema


    /**
     * @param int $id primary key of active record.
     * It tries to get element from table with such identifier
     */
    public function __construct($id = 0)
    {
        $id = (int) $id;

        $reflection = new \ReflectionClass($this);
        $this->_information['namespace'] = $reflection->getParentClass()->name;
        $this->_information['tableName'] = lcfirst($reflection->getShortName());
        $this->_information['schema'] = App::$db->describeTable($this->_information['tableName']);


        if($id > 0){
            $this->getRow(['id'=>$id]);
        }
    }


    /**
     * @param array|bool $where conditions that passed as ['column'=>'columnValue', ...]
     * @return array  AR fields and data as assoc array if it exists
     */
    public function getRow($where = false, $type = self::OBJ)
    {
        $data = App::$db->getRecord(['*'], $this->_information['tableName'], $where);

        $this->declareFields($data);

        return $this->getFieldsVars($type);
    }


    /**
     * Declares and fills fields of AR by passing columns from table schema to class properties
     * Also this function declares properties value if its type matches
     *
     * If AR contains some existing row it will have defined identifier. Otherwise id will be unset because it shall not
     * be set manually
     * @param null|array $data AR's columns values
     */
    protected function declareFields($data = null)
    {

        if(empty($this->_information['schema'])) {
            return;
        }


        foreach($this->_information['schema'] as $field) {

            if ($data && isset($data[$field['Field']])) {
                $this->$field['Field'] = $data[$field['Field']];
            } else {
                switch ($field['Key']) {
                    case 'PRI':
                        unset($this->$field['Field']); //While saving AR it shall not try save primary key
                        break;
                    default:
                        $this->$field['Field'] = $field['Default'];
                        break;
                }

                if ($field['Key'] != 'PRI' && $field['Null'] == 'NO' && empty($this->$field['Field'])) {
                    $this->$field['Field'] = '';
                }

                // if current dateTime value is empty(maybe tis new record)
                if ($field['Type'] == 'datetime' && empty($this->field)) {
                    $this->$field['Field'] = date("Y-m-d G:i:s");
                }
            }
        }
    }


    /**
     * Tries to save containing data to database
     * It is necessary for record to have id key as long as it is mostly used as primary key
     * If record has id, method will update row otherwise new row will be inserted
     * @return bool if AR data save is successful
     */
    public function save()
    {
        $data = $this->getFieldsVars();


        if($data['id'] > 0){
            if (App::$db->updateRecord($data,  $this->_information['tableName'], ['id'=>$data['id']])){
                return true;
            }
        } else{
            if (App::$db->addRecord($data, $this->_information['tableName'])){
                return true;
            }
        }

        return false;
    }


    /**
     * Returns records variables that are declared as active record class's properties
     * Method checks if first letter of property name is _ that is kept for system information(non-active records columns)
     *
     * @return array of AR's fields
     */
    protected function getFieldsVars($type)
    {
        $fields = [];
        foreach($this as $name=>$value){
            if($name[0] !== '_') {
                $fields[$name] = $value;
            }
        }

        return $type === self::OBJ ? (object) $fields : $fields;
    }

}