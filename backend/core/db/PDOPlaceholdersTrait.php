<?php

namespace core\db;


/**
 * Class PDOPlaceholdersTrait
 * @package core\db
 */
trait PDOPlaceholdersTrait
{

    private $bool = \PDO::PARAM_BOOL;
    private $str = \PDO::PARAM_STR;
    private $int = \PDO::PARAM_INT;
    private $null = \PDO::PARAM_NULL;


    /**
     * @param string $placeHolder
     * @return int code for PDO placeholder parameter
     */
    protected function getPDOParam($placeHolder)
    {
        $placeHolder = strtolower($placeHolder);
        return ($placeHolder && isset($this->{$placeHolder})) ? $this->{$placeHolder} : $this->str;
    }

}