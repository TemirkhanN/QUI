<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 12.05.2015
 * Time: 22:48
 */

namespace app\core\base;


class Model {

    protected $errors;


    /**
     * @param string $method logging error message
     * @param string $error logging error message
     */
    protected function logError($method = '', $error = '')
    {
        $this->errors[$method][] = $error;
    }



    protected function hasErrors($method = '')
    {
        if(!empty($this->errors[$method])){
            return true;
        }
        return false;
    }




    protected function returnErrors($method = '')
    {
        if($this->hasErrors($method))
        {
            return $this->errors[$method];
        }

        return [];

    }




}