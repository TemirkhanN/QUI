<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 12.05.2015
 * Time: 22:26
 */

namespace app\models\user;


use app\core\base\Model;

class User extends Model {

    public $authorized;
    public $data;
    private static $instance;



    public function __construct()
    {
        if(self::$instance == null) {
            $this->authorized = $this->checkAuthorization();
            self::$instance = $this->authorized;
        }

        return self::$instance;
    }



    public function logIn($login = '', $password = '')
    {

        if(!$this->authorized) {
            if ($this->validateLogin($login) && $this->validatePassword($password)) {

                $user = new \app\models\database\User();
                $user->getRecord(['login' => $login]);


                if ($user->login === strtolower($login) && password_verify($password, $user->password)) {
                    $this->authorized = true;
                    $this->saveAuthorization((array) $user);
                    return true;
                } else {
                    $this->logError('logIn', 'Пользователя с таким логином или паролем не существует');
                }

            } else {
                return array_merge($this->returnErrors('validatePassword'), $this->returnErrors('validateLogin'));
            }
        } else{
            $this->logError('logIn', 'Вы уже авторизованы');
        }


        return $this->returnErrors(__FUNCTION__);
    }




    private function validatePassword($password = '')
    {
        $valid = true;

        if (empty($password)){
            $this->logError('validatePassword', 'Пароль пуст');
            $valid = false;
        }

        return $valid;
    }



    private function validateLogin($login = '')
    {
        $valid = true;
        if(empty($login)){
            $this->logError('validateLogin', 'Логин пуст');
        }

        return $valid;
    }




    private function checkAuthorization()
    {
        if(!empty($_SESSION['user'])){

            if(empty($this->data)){
                $this->data = $_SESSION['user'];
            }

            return true;
        }

        return false;
    }


    private function saveAuthorization($userData = [])
    {
        $_SESSION['user'] = $userData;
    }



    public function logOut()
    {
        $this->authorized = false;
        $this->data = null;
        $_SESSION['user'] = [];
    }




    public function getProfile($userId = 0)
    {

        $userId = intval($userId) == 0 ? $this->data['id'] : intval($userId);

        $user = new \app\models\database\User();
        if($user = $user->getRecord(['id' => $userId], $user::OBJ)){
            return $user;
        } else{
            $this->logError('getProfile', 'Пользователя с таким ид не существует');
            return $this->returnErrors('getProfile');
        }
    }

}