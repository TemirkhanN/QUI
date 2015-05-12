<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 12.05.2015
 * Time: 15:03
 */

namespace app\core\db;


use app\core\Application;

class ActiveRecord{

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
        $this->_information['schema'] = Application::$db->describeTable($this->_information['tableName']);


        if($id != null){
            $data = Application::$db->getRecord(['*'], $this->_information['tableName'], ['id'=>$id]);
        }

        $this->declareFields($data);
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
            if (Application::$db->updateRecord($data,  $this->_information['tableName'], ['id'=>$data['id']])){
                return true;
            }
        } else{
            if (Application::$db->addRecord($data, $this->_information['tableName'])){
                return true;
            }
        }

        return false;
    }



    public function getRecord($where = [])
    {

        $data = Application::$db->getRecord(['*'], $this->_information['tableName'], $where);

        if($data){
            $this->declareFields($data);
            return true;
        }

        return false;
    }


    public function returnData()
    {
        $data = (array) $this;
        return $this->unsetPrivateVariables($data);
    }




    private function unsetPrivateVariables($data = [])
    {

        foreach(array_keys($data) as $name){
            if(strpos($name, $this->_information['namespace'])){
                unset($data[$name]);
            }
        }

        return $data;
    }

}