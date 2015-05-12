<?php
/**
 * Created by PhpStorm.
 * User: temirkhan
 * Date: 17.04.15
 * Time: 11:26
 */

namespace app\core\db;



class Connection {

    protected $connection = null;



    public function connectedSuccessfully()
    {
        if($this->connection){
            return true;
        }

        return false;
    }



} 