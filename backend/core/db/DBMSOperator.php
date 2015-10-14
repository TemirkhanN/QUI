<?php

namespace core\db;


abstract class DBMSOperator implements DBMSOperatorInterface
{

    use PDOPlaceholdersTrait;

    protected $connection;


    protected $actionLocked = false;


    final public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }


    /**
     * @return bool calls in every CRUD-like action to prevent another action before previous completed or reset
     */
    final protected function actionLock()
    {
        if ($this->actionLocked) {
            return false;
        }

        $this->actionLocked = true;

        return true;
    }


    /**
     * @return bool unlocks connection for another CRUD-like action
     */
    final protected function actionUnlock()
    {
        if (!$this->actionLocked) {
            return false;
        }
        $this->actionLocked = false;

        return true;
    }


    /**
     * @param string $query
     * @return mixed
     */
    abstract public function query($query);


    abstract protected function buildQuery();


    abstract public function launch();


    /**
     * @param null | string $name
     * @return mixed
     */
    abstract public function lastInsertId($name = null);


    /**
     * @param string $tableName
     * @return bool
     */
    abstract public function tableExists($tableName);


    /**
     * @param $tableName
     * @return array | null
     */
    abstract public function tableSchema($tableName);


    /**
     * @param array $data
     * @return $this
     */
    abstract public function add($data = []);


    /**
     * @param array $fields
     * @return $this
     */
    abstract public function get($fields = []);


    /**
     * @param array $data
     * @return $this
     */
    abstract public function update($data = []);


    /**
     * @return $this
     */
    abstract public function delete();


    /**
     * @param $source
     * @return $this
     */
    abstract public function from($source);


    /**
     * Alias for $this->from()
     *
     * @param $source
     * @return $this
     */
    public function to($source)
    {
        return $this->from($source);
    }


    /**
     * @param $condition
     * @return $this
     */
    abstract public function where($condition);


    /**
     * @param $condition
     * @return $this
     */
    abstract public function andWhere($condition);


    /**
     * @param $condition
     * @return $this
     */
    abstract public function orWhere($condition);


    /**
     * @param array $group
     * @return $this
     */
    abstract public function group($group = []);


    /**
     * @param array $order
     * @return $this
     */
    abstract public function order($order = []);


    /**
     * @param int $offset
     * @return $this
     */
    abstract public function offset($offset = 0);


    /**
     * @param int $limit
     * @return $this
     */
    abstract public function limit($limit = 1);

}