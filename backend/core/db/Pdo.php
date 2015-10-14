<?php

namespace app\core\db;


use app\core\App;


/**
 * Class PdoWrapper
 * @package app\core\db
 *
 * Mother of all database actions. Tis some kind of cover of getting, checking, deleting and updating queries
 * Comments are in russian. It will be changed soon
 */
class PdoWrapper extends Connection implements DbManagementSystem{





    public function __construct($config)
    {

        try{
            $db = new \PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['name'] . ';charset=utf8', $config['user'], $config['password']);
        } catch(\PDOException $e){
            App::noteError($e);
        }

        if(isset($db)){
            $this->connection = $db;
        }

    }





    public function query($query)
    {
        return $this->connection->query($query);
    }




    public function describeTable($tableName = '')
    {
        if($this->checkTableExist($tableName)){
            if($fields = $this->executeQuery('DESCRIBE ' . $tableName)){
                return $fields->fetchAll(\PDO::FETCH_ASSOC);
            }
        }
    }




    public function checkTableExist($tableName = '')
    {

        if($this->executeQuery("SHOW TABLES LIKE ?", [$tableName])->fetch()){
            return true;
        }

        return false;
    }



    /**
     * @param array $data = ['columnName'=>'columnValue'];
     * @param string $tableName имя таблицы
     * @return mixed возвращает ид добавленной записи или false, в случае неудачи
     */

    public function addRecord($data = [], $tableName = '')
    {

        $query = "INSERT INTO `{$tableName}` SET ";

        foreach (array_keys($data) as $columnName){
            $params[] = $columnName .' = :' . $columnName . ' ';

        }

        if (!empty($params)){
            $query .= implode(',', $params);
        }


        $record = $this->connection->prepare($query);

        $record->execute($data);

        if ($record->errorInfo()[1] !== null){
            $this->pdoError($record);
            return false;
        } else{
            return $this->lastInsertId();
        }

    }


    /**
     * @param array $data
     * @param string $tableName
     * @param array $where параметры, указывающие на запись, которую нужно обновить
     */
    public function updateRecord($data = [], $tableName = '', $where= [])
    {

        $query = "UPDATE `{$tableName}` SET ";

        foreach (array_keys($data) as $columnName){
            $params[] = $columnName .' = :' . $columnName . ' ';
        }

        if (!empty($params)){
            $query .= implode(',', $params);
        }

        $query .= $this->where($where);

        $record = $this->connection->prepare($query);

        $record->execute($data);


        if ($record->errorInfo()[1] !== null){
            return false;
        } else{
            return true;
        }




    }


    /**
     * @param array|string $columns извлекаемые поля
     * @param string $tableName название таблицы, из которой пытаемся получить запись
     * @param array $where условия, которые передаются в виде ассоциативного массива ключ=значение
     * @param array $sort сортировка КЛЮЧ ЗНАЧЕНИЕ (column ASC/DESC)
     * @param int $offset selecting element offset if somehow needed
     * @return mixed возвращает ассоциативный результат выборки, саму выборку, если извлекалось только 1 поле или false, если нет результатов
     */



    public function getRecord($columns = ['*'], $tableName = '', $where = [], $sort = [], $offset = 0)
    {

        $this->query = $this->buildQuery($columns, $tableName, $where,  $sort, $offset, 1);
        $record = $this->executeQuery($this->query, $where);

        if ($record){
            $record = $record->fetch(\PDO::FETCH_ASSOC);

            if(count($record) === 1 && !is_array($columns) && isset($record[$columns])){
                return  $record[$columns];
            } else{
                return $record;
            }
        }

        return false;

    }


    /**
     * @param array $columns извлекаемые поля
     * @param string $tableName название таблицы, из которой пытаемся получить запись
     * @param array $where условия, которые передаются в виде ассоциативного массива ключ=значение
     * @param array $sort сортировка КЛЮЧ ЗНАЧЕНИЕ (column ASC/DESC)
     * @param int $offset сдвиг выборки
     * @param int $limit количество извлекаемых записей
     * @return mixed возвращает ассоциативный результат выборки или false, в случае ее отсутствия
     */


    public function getRecords($columns = ['*'], $tableName ='', $where = [],  $sort = [], $offset = 0, $limit = 0)
    {

        $this->query = $this->buildQuery($columns, $tableName, $where,  $sort, $offset, $limit);
        $record = $this->executeQuery($this->query, $where);

        if ($record){
            return $record->fetchAll(\PDO::FETCH_ASSOC);
        }

        return false;
    }



    public function countRecords($tableName = '', $where = [])
    {
        $count = $this->getRecord(['COUNT(*) as total'], $tableName, $where);

        return !empty($count) && !empty($count['total']) ? $count['total'] : 0;
    }



    private function buildQuery($columns = ['*'], $tableName ='', $where = [],  $sort = [], $offset = 0, $limit=0)
    {
        $query = $this->selectColumns($columns);

        $query .= $this->from($tableName);

        $query .= $this->sort($sort);

        $query .= $this->where($where);

        $query .= $this->limit($offset, $limit);


        return $query;

    }


    /**
     * @param string $query подготовленное выражение
     * @param array $params параметры подготовленного выражения
     * @return bool|\PDOStatement
     */

    public function executeQuery($query = '', $params = [])
    {

        $record = $this->connection->prepare($query);

        array_walk($params, [$this, 'makeWhereParams']);

        $record->execute($params);

        if ($record->errorInfo()[1] !== null){

            $this->pdoError($record);
            return false;

        } else{
            return $record;
        }

    }



    private function makeWhereParams($params = [])
    {

        if(!is_array($params)){
            return;
        }

        foreach($params as $key=>$value){
            if(is_array($value)){
                $params[$key] = $value[1];
            }
        }

    }



    /**
     * @param array|string $columns извлекаемые поля ['column1','another_column'] или 'column1'. по-умолчанию * - все поля
     * @return string
     */
    private function selectColumns($columns = ['*'])
    {
        $query = "SELECT ";

        $query .= is_array($columns) ? implode(', ', $columns) : $columns;

        return $query;

    }


    private function from($from = '')
    {
        $query = " FROM `{$from}`";


        return $query;
    }





    /**
     * @param $where ['id'=>14, 'column'=>'somevalue'];
     * @return string
     */

    private function where($where = [])
    {

        $query = '';
        $params = [];
        if (!empty($where)){

            $query .= ' WHERE ';

            foreach ($where as $columnName=>$value){

                if(is_array($value)){
                    switch(strtoupper($value[0])){
                        case 'IN':
                            $params[] = $columnName .' IN(:' . $columnName . ')';

                    }
                } else{
                    $params[] = $columnName .' = :' . $columnName . ' ';
                }

            }

            $query .= implode('AND ', $params);
        }
        $this->queryParams = $params;

        return $query;
    }





    /**
     * @param array $sort = ['id'=>'ASC', 'column'=>'DESC'];
     * @return string
     */

    private function sort($sort = [])
    {

        $query = ' ORDER BY ';
        $params = [];

        foreach ($sort as $columnName=>$sortType){

            if ($this->checkValidSort($sortType, $columnName)){
                $params[] = $columnName.' '.$sortType;
            }

        }

        if (empty($params)){
            $query = '';
        }

        $query .= implode(', ', $params);

        return $query;


    }


    /**
     * @param int $offset сдвиг выборки
     * @param int $limit количество извлкаемых записей
     * @return string
     */
    private function limit($offset = 0, $limit = 0)
    {
        $limit = intval($limit);
        $offset = intval($offset);

        if ($limit==0){
            return '';
        } else{
            return ' LIMIT ' . $offset . ', ' .$limit;
        }
    }


    /**
     * @param $type тип сортировки проверяется на соответствие ASC или DESC
     * @param $column сортируемое поле проверяется на соответствие регуоярному выражению
     * @return bool возвращает true в случае, если сортировка и сортируемое поле удовлетворяют условиям
     */
    private function checkValidSort($type, $column)
    {

        $type = strtolower($type);

        if (($type === 'desc' || $type === 'asc') && preg_match('#^[\w_\d]{2,}#i', $column)) {
            return true;
        }

        return false;

    }


    /**
     * @return string ид последней, вставленной в таблицу, записи
     */
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }


    /**
     * @param string $string строка, которую нужно экранировать и заключить в ковычки
     * @return string
     */
    public function quote($string = '')
    {
        return $this->connection->quote($string);
    }





    public function pdoError($query)
    {

        echo $query->errorInfo()[2] .'<br>';


    }



} 