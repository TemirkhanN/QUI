<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 15.05.2015
 * Time: 23:01
 */

namespace app\controllers;


use app\core\base\Controller;
use app\models\user\User;

class UserController extends Controller {


    public function pageLogin()
    {

        $user = new User();
        $errors = null;


        if(!$user->authorized()) {
            if (isset($_POST['log_in'])) {
                $loggedIn = $user->logIn($_POST['login'], $_POST['password']);
                if ($loggedIn === true) {
                    $this->redirect("/");
                } else {
                    $errors = $loggedIn;
                }
            }
        } else {
            $this->redirect("/");
        }

        $this->setCustomTemplate(true);

        $this->renderPage('login', [
                'login_errors'=>$errors,
                'user'=>$user
            ]
        );

    }




    public function pageLogout()
    {

        (new User())->logOut();
        $this->redirect('/');
    }



    public function pageProfile()
    {
        $errors = false;
        $user = new User();
        $profile = $user->getProfile();

        if(!isset($profile->id)){
            $errors = true;
        }

        $this->renderPage('profile', [
            'user' => $user,
            'profile' => $profile,
            'errors' => $errors
        ]);
    }

}