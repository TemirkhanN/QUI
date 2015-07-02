<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 12.05.2015
 * Time: 22:26
 */

namespace app\models\user;


use app\core\App;
use app\core\base\Model;

class User extends Model
{

    private $authorized;
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




    private function initializeUserTable()
    {
        if(App::$db->checkTableExist('user') === false){
            App::$db->query("CREATE TABLE IF NOT EXISTS `user` (
                `id` INT(11) AUTO_INCREMENT,
                `login` VARCHAR(255) NOT NULL DEFAULT '',
                `password` VARCHAR(255) NOT NULL DEFAULT '',
                `privileges` VARCHAR(20) NOT NULL DEFAULT 'user',
                `regdate` DATETIME NOT NULL,
                PRIMARY KEY(`id`),
                UNIQUE KEY `login` (`login`)
               ) ENGINE=InnoDB  DEFAULT CHARSET=utf8
            ");
            App::$db->addRecord(['login'=>'admin', 'password'=>password_hash('admin', PASSWORD_DEFAULT), 'privileges'=>'admin'], 'user');

        }


    }





    public function logIn($login = '', $password = '')
    {

        if(!$this->authorized) {
            if ($this->validateLogin($login) && $this->validatePassword($password)) {

                self::initializeUserTable();

                $user = new \app\models\database\User();
                $user->getRecord(['login' => $login]);


                if ($user->login === strtolower($login) && password_verify($password, $user->password)) {
                    $this->authorized = true;
                    $this->saveAuthorization((array) $user);
                    return true;
                } else {
                    $this->logError('logIn', "User doesn't exist");
                }

            } else {
                return array_merge($this->returnErrors('validatePassword'), $this->returnErrors('validateLogin'));
            }
        } else{
            $this->logError('logIn', "You're already authorized");
        }


        return $this->returnErrors(__FUNCTION__);
    }




    private function validatePassword($password = '')
    {
        $valid = true;

        if (empty($password)){
            $this->logError('validatePassword', 'Password is empty');
            $valid = false;
        }

        return $valid;
    }



    private function validateLogin($login = '')
    {
        $valid = true;
        if(empty($login)){
            $this->logError('validateLogin', 'Login is empty');
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




    public static function authorized()
    {
        return self::$instance == null ? false : self::$instance;
    }




    private function saveAuthorization($userData = [])
    {
        $_SESSION['user'] = $userData;
    }





    public static function hasRights($rights = 'user')
    {

        if(self::authorized() === false){
            return false;
        }

        return  !empty($_SESSION['user']['privileges']) && $_SESSION['user']['privileges'] == $rights;
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
            $this->logError('getProfile', "user with such identifier doesn't exist");
            return $this->returnErrors('getProfile');
        }
    }

}