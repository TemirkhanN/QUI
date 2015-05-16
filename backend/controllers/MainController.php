<?php
/**
 * Created by PhpStorm.
 * User: temirkhan
 * Date: 17.04.15
 * Time: 17:11
 */

namespace app\controllers;


use app\core\App;
use app\core\base\Controller;

class MainController extends Controller {


    //Главная страница сайта
    public function pageIndex()
    {
        $this->redirectToController('main/example');

    }


    public function pageExample()
    {
        $variable = 'Hello World';

        $this->setTitle('Bootstrap showcase');

        $this->renderPage('index', ['var'=>$variable]);
    }


    public function pageError404()
    {
        $this->renderPage('error404');
    }




}