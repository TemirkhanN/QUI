<?php

namespace app\core\db;


/**
 * Class Connection
 * @package app\core\db
 *
 * Basic database connection parent class.
 * For future. Maybe it shall be abstract. Don't know
 */
class Connection {

    protected $connection = null;

    protected $query = null;



    public function connectedSuccessfully()
    {
        if($this->connection){
            return true;
        }

        return false;
    }



} 