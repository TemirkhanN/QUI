<?php

namespace core\db\mysql;


use \core\db\DBMSOperator;

class MysqlOperator extends DBMSOperator
{
    const MAX_LIMIT = '18446744073709551615';


    private $action = ''; //SELECT, DELETE, UPDATE and etc..
    private $columns = []; //Column names default is ['*']
    private $from = []; //Table names ['table1', 'table2']
    private $where = []; //Conditions
    private $group = []; // Group ['field','field2']
    private $order = []; //Order ['field'=>'DESC', 'anotherField' => 'ASC']
    private $offset = 0;
    private $limit = 0;

    private $query = '';
    private $data = [
        'values' => [],
        'placeholders' => []
    ];


    /**
     * All variables shall be set to default here. Sorry for that
     */
    public function resetOperation()
    {
        $this->action = 'SELECT';
        $this->columns = [];
        $this->from = [];
        $this->where = [];
        $this->order = [];
        $this->group = [];
        $this->offset = 0;
        $this->limit = 0;

        $this->query = '';
        $this->data = [
            'values' => [],
            'placeholders' => []
        ];
    }


    /**
     * @param $query
     * @return \PDOStatement | false
     */
    public function query($query)
    {
        return $this->connection->query($query);
    }


    /**
     * @param $query
     * @return \PDOStatement
     */
    public function prepare($query)
    {
        return $this->connection->prepare($query);
    }


    /**
     * @param string $query prepared query
     * @param array $values
     * @return \PDOStatement | false
     */
    public function executePreparedQuery($query, $values = [])
    {
        $statement = $this->prepare($query);
        if ($statement) {
            $this->bindValues($statement, $values);
            $statement->execute();
        }

        return $statement;
    }


    /**
     * Binds values to passed statement by reference. Values shall input as array with placeholders keys
     * that contains array with value and value_type or string with value.
     * In second case any value will be bind as string
     *
     * Value types represented in PDOPlaceholders
     * str, int, bool, null
     *
     *
     * $placeholders[
     *      'placeholder_name'=>[
     *          'value',
     *          'type'
     *       ],
     *      'another_placeholder'=>'another_value',
     * ];
     *
     *
     * @param \PDOStatement $statement
     * @param array $values parameters for prepared query.
     * @return null | void
     */
    public function bindValues(&$statement, $values)
    {
        if (!is_array($values)) {
            return null;
        }

        foreach ($values as $placeholder => $data) {

            if (is_array($data)) {
                $value = $data['value'];
                $type = $data['type'];
            } else {
                $value = $data;
                $type = 'str';
            }

            $statement->bindValue(':' . $placeholder, $value, $this->getPDOParam($type));
        }

    }


    /**
     * @param null | string $name
     * @return int id of last inserted row
     */
    public function lastInsertId($name = null)
    {
        return $this->connection->lastInsertId($name);
    }


    private function setAction($action)
    {
        if ($this->actionLock()) {
            $this->action = $action;
        } else {
            return new \Exception('You have incomplete action');
        }
        return true;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function add($data = ['column' => ['value', 'type']])
    {
        if ($this->setAction('INSERT')) {
            $this->data['values'] = $data;
        }
        return $this;
    }


    /**
     * @param array $columns
     * @return $this
     */
    public function get($columns = ['*'])
    {
        if ($this->setAction('SELECT')) {
            $this->columns = $columns;
        }
        return $this;
    }


    /**
     * @param array $updatingData
     * @return $this
     */
    public function update($updatingData = ['column' => ['value', 'type']])
    {
        if ($this->setAction('UPDATE')) {
            $this->data['values'] = $updatingData;
        }
        return $this;
    }


    /**
     * @return $this
     */
    public function delete()
    {
        $this->setAction('DELETE');

        return $this;
    }


    /**
     * @param string $tableName
     * @return $this
     */
    public function from($tableName)
    {
        $this->from[] = $tableName;
        return $this;
    }


    /**
     * Compose "field='value' OR another_field<12"
     *
     * @param array $condition
     * @return $this
     */
    public function where($condition = null)
    {
        if($condition!==null){
            $this->where[] = '(' . $condition . ')';
        }

        return $this;
    }


    /**
     * Compose "field='value' OR another_field<12"
     *
     * @param string $condition
     * @return $this
     */
    public function andWhere($condition)
    {
        $this->where[] = 'AND (' . $condition . ')';

        return $this;
    }


    /**
     * Compose "field='value' OR another_field<12"
     *
     * @param array $condition
     * @return $this
     */
    public function orWhere($condition)
    {
        $this->where[] = 'OR (' . $condition . ')';

        return $this;
    }


    /**
     * @param array $order
     * @return $this
     */
    public function order($order = [])
    {
        $this->order = $order;

        return $this;
    }


    /**
     * @param int $offset
     * @return $this
     */
    public function offset($offset = 0)
    {
        $this->offset = abs($offset);

        return $this;
    }


    /**
     * @param int $limit
     * @return $this
     */
    public function limit($limit = 1)
    {
        $this->limit = abs($limit);

        return $this;
    }


    /**
     * @param array $group
     * @return $this
     */
    public function group($group = [])
    {
        $this->group = $group;

        return $this;
    }


    /** General query builder that decide which type of queries shall be built
     * @return string
     */
    protected function buildQuery()
    {

        switch ($this->action) {
            case 'INSERT':
                $this->buildInsertQuery();
                break;
            case 'SELECT':
                $this->buildSelectQuery();
                break;
            case 'UPDATE':
                $this->buildUpdateQuery();
                break;
            case 'DELETE':
                $this->buildDeleteQuery();
                break;
            default:
                $this->buildSelectQuery();
        }

        return $this->query;
    }


    /**
     * @return void
     */
    private function buildInsertQuery()
    {
        $this->query = 'INSERT INTO';
        $this->implementFrom();
        $this->implementInsertFields();
    }


    /**
     * @return void
     */
    private function buildSelectQuery()
    {
        $this->query = 'SELECT';
        $this->implementColumns();
        $this->query .= ' FROM';
        $this->implementFrom();
        $this->implementConditions();
        $this->implementGroup();
        $this->implementOrder();
        $this->implementLimit();
    }


    /*
     * @return void
     */
    private function buildUpdateQuery()
    {
        $this->query = 'UPDATE';
        $this->implementFrom();
        $this->query .= ' SET';
        $this->implementUpdateFields();
        $this->implementConditions();
        $this->implementOrder();
        $this->implementLimit();
    }


    /*
     * @return void
     */
    private function buildDeleteQuery()
    {
        $this->query = 'DELETE';
        $this->query .= ' FROM';
        $this->implementFrom();
        $this->implementConditions();
        $this->implementOrder();
        $this->implementLimit();
    }


    /*
     * @return void
     */
    private function implementColumns()
    {
        $this->query .= ' ' . implode(', ', $this->columns);
    }


    /*
     * @return void
     */
    private function implementFrom()
    {
        $this->query .= ' ' . implode(', ', $this->from);
    }


    /**
     * Prepares placeholders and their values to be easily used in operations implementation
     *
     * @return void
     */
    private function implementsPlaceholders()
    {

        foreach ($this->data['values'] as $field => $dataInfo) {
            if (!$dataInfo) {
                unset($this->data['values'][$field]);
                continue;
            }
            if (is_array($dataInfo)) {
                $value = $dataInfo[0];
                $type = isset($dataInfo[1]) ? $dataInfo[1] : 'str';
            } else {
                $value = $dataInfo;
                $type = 'str';
            }

            $this->data['placeholders'][$field] = ['value' => $value, 'type' => $type];
        }
    }


    /**
     * return @void
     */
    private function implementInsertFields()
    {
        $this->implementsPlaceholders();

        $placeholders = [];
        foreach (array_keys($this->data['placeholders']) as $placeholder) {
            $placeholders[] = ':' . $placeholder;
        }

        $this->query .= '(' . implode(', ', array_keys($this->data['values'])) . ')';
        $this->query .= ' VALUES(' . implode(', ', $placeholders) . ')';
    }


    /**
     * return @void
     */
    private function implementUpdateFields()
    {
        $updateQuery = '';
        $this->implementsPlaceholders();

        if (!empty($this->data['values'])) {
            foreach (array_keys($this->data['values']) as $column) {
                $updateQuery .= ' ' . $column . '=:' . $column . ',';
            }

            $this->query .= mb_strcut($updateQuery, 0, -1); //To delete last comma
        }
    }


    /*
     * @return void
     */
    private function implementConditions()
    {
        if (!empty($this->where)) {
            $this->query .= ' WHERE ' . implode(' ', $this->where);
        }
    }


    /**
     * @return void
     */
    private function implementGroup()
    {
        if (!empty($this->group)) {
            $this->query .= ' GROUP BY ' . implode(', ', $this->group);
        }
    }


    /**
     * @return void
     */
    private function implementOrder()
    {
        foreach ($this->order as $orderBy => $orderType) {
            $order[] = $orderBy . ' ' . $orderType;
        }

        if (!empty($order)) {
            $this->query .= ' ORDER BY ' . implode(', ', $order);
        }
    }


    /* Implements limit and offset values to query string.
     *
     * @return void
     */
    private function implementLimit()
    {
        $offset = $this->offset ? $this->offset . ', ' : '';
        $limit = $this->limit ? $this->limit : self::MAX_LIMIT;

        $this->query .= ' LIMIT ' . $offset . $limit;
    }


    /**
     * Launches query with all outgoing operations.
     * Checks if query has placeholders and executes prepared query if so. Otherwise simple query will be executed
     *
     *
     * @return \PDOStatement | false
     */
    public function launch()
    {
        $this->actionUnlock();
        $query = $this->buildQuery();

        if (empty($this->data['placeholders'])) {
            $statement = $this->query($query);
        } else {
            $statement = $this->executePreparedQuery($query, $this->data['placeholders']);
            if ($this->statementError($statement)) {
                $statement = false;
            }
        }

        $result = $this->action === 'SELECT' ? $this->fetchAll($statement) : $statement;
        $this->resetOperation();
        return $result;
    }


    /**
     * @param \PDOStatement $queryResult
     * @return array|bool
     */
    private function fetchAll($queryResult)
    {
        if (!$queryResult || !$queryResult instanceof \PDOStatement) {
            return false;
        }

        $result = $queryResult->fetchAll(\PDO::FETCH_ASSOC);

        return empty($result) ? false : $result;
    }


    /**
     * Returns if there error occurred in statement or error message if second arg set to true
     *
     * @param \PDOStatement $statement
     * @param bool|false $getErrorMessage
     *
     * @return bool | string
     */
    private function statementError($statement, $getErrorMessage = false)
    {
        $errorInfo = $statement->errorInfo();
        $errorMessage = $errorInfo[2] !== null ? $errorInfo[2] : 'There was no error in statement';

        return $getErrorMessage ? $errorMessage : $errorInfo[0] !== '00000';
    }


    /**
     * @param string $tableName
     * @return array|null
     */
    public function tableSchema($tableName = '')
    {
        if ($this->tableExists($tableName)) {
            if ($fields = $this->executePreparedQuery('DESCRIBE :tableName', ['tableName' => $tableName])) {
                $schema = $fields->fetchAll(\PDO::FETCH_ASSOC);
                $fields = [];
                foreach ($schema as $field) {
                    $fields[$field['Field']] = $field['Type'];
                }

                return $fields;
            }
        }

        return null;
    }


    /**
     * @param string $tableName
     * @return bool
     */
    public function tableExists($tableName = '')
    {
        $exists = $this->executePreparedQuery('SHOW TABLES LIKE :tableName', ['tableName' => $tableName]);
        if ($exists && $exists->fetch()) {
            return true;
        }

        return false;
    }


}