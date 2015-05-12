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
     * @param string $method contains method name where error occurred
     * @param string $error logging error message
     */
    protected function logError($error = '')
    {
        $detailedInfo = debug_backtrace();
        $occurredIn = $detailedInfo[0]['file'];
        $i = 0;

        while($detailedInfo[$i]['file'] == $occurredIn){
            $errorMethod = $detailedInfo[$i]['function'];
            $this->errors[$errorMethod] = $errorMethod;
            ++$i;
        }
        $this->errors[$errorMethod] = $error;
        unset($detailedInfo);
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

        return false;

    }




}