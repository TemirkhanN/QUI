<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 12.05.2015
 * Time: 15:03
 */

namespace app\core\db;


use app\core\App;

class ActiveRecord{


    const ARR = 'array';
    const OBJ = 'object';

    public $id; // Эта переменная всегда должна быть объявлена
    private $_information = []; // Здесь хранится необходимая системная информация
    private $_reflection;


    /**
     * @param int $id при передаче значения, пытается найти элемент с указанным идентификатором
     */
    public function __construct($id = 0)
    {
        $this->id = null;
        $data = null;

        $this->_reflection = new \ReflectionClass($this);
        $this->_information['namespace'] = $this->_reflection->getParentClass()->name;
        $this->_information['tableName'] = lcfirst($this->_reflection->getShortName());
        $this->_information['schema'] = App::$db->describeTable($this->_information['tableName']);


        if($id != null){
            $this->getRow(['id'=>$id]);
        }

    }


    /**
     * @param null|array $data значения полей в записи
     */
    protected function declareFields($data = null)
    {

        if(!empty($this->_information['schema'])){
            foreach($this->_information['schema'] as $field){

                if(isset($data[$field['Field']])){
                    $this->$field['Field'] = $data[$field['Field']];
                } else{
                    switch($field['Key']){
                        case 'PRI':
                            unset($this->$field['Field']);
                            break;
                        default:
                            $this->$field['Field'] = $field['Default'];
                            break;
                    }

                    if($field['Key'] != 'PRI' && $field['Null']=='NO' && empty($this->$field['Field'])) {
                        $this->$field['Field'] = '';
                    }

                    if($field['Type'] == 'datetime' &&  empty($this->field)){
                        $this->$field['Field'] = date("Y-m-d G:i:s");
                    }
                }
            }
        }

    }



    public function save()
    {

        $data = $this->returnData();


        if($data['id']){
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



    public function getRow($where = [])
    {

        $data = App::$db->getRecord(['*'], $this->_information['tableName'], $where);

        if($data){
            $this->declareFields($data);
            return true;
        }

        return false;
    }

    public function getRecord($where = [], $type = self::ARR)
    {

        $this->getRow($where);

        return $this->returnData($type);

    }


    public function returnData($type = self::ARR)
    {
        $data = $type == self::ARR ? (array) $this : $this;
        return $this->unsetPrivateVariables($data, $type);
    }




    private function unsetPrivateVariables($data, $type = self::ARR)
    {
        foreach($data as $name=>$value){

            if($type == self::ARR) {
                if(strpos($name, $this->_information['namespace'])){
                    unset($data[$name]);
                }
            } elseif($type == self::OBJ){
                if($name[0] == '_') {
                    unset($data->{$name});
                }
            }
        }

        return $data;
    }

}