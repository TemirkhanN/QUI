<?php

namespace core\base;


/**
 * Class Model
 * @package core\base
 *
 * Extremely raw for now and has only methods that check, log or return errors that happened during child
 * class execution.
 *
 * It exists for easy way to return errors that shall be handled(not throwed exception or interrupt script execution)
 *
 * For example: User class uses error messages display when login or password are incorrect or maybe user with such data doesn't exist
 * It's necessary to pass method name via __FUNCTION__ for flexible models.
 * Use manual method name pass when it really needed. Let it be you want check if there errors on login validation from logIn method.
 * $this->hasErrors('loginValidation');
 *
 * $this->logError(__FUNCTION__, "User doesn't exist");
 *
 *
 * This class is for future extensibility!!!
 *
 */
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


    /**
     * @param string $method name of function that may have errors
     * @return bool true if method has made error(s)
     */
    protected function hasErrors($method = '')
    {
        if(!empty($this->errors[$method])){
            return true;
        }
        return false;
    }


    /**
     * @param string $method name of function that may have errors
     * @return array will be empty if has no errors . Otherwise errors as array
     * ['password is lesser than 5 chars', 'password shall have 1 uppercase sign']
     */
    protected function returnErrors($method = '')
    {
        if($this->hasErrors($method))
        {
            return $this->errors[$method];
        }

        return [];

    }




}