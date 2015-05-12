<?php
/**
 * Created by PhpStorm.
 * User: temirkhan
 * Date: 17.04.15
 * Time: 17:11
 */

namespace app\controllers;


use app\core\base\Controller;

class Main extends Controller {


    //Главная страница сайта
    public function pageIndex()
    {

        $variable = 'Hello World';


        $this->setTitle('About us');



        $this->renderPage('index', ['var'=>$variable]);


    }


    public function pageError404()
    {
        $this->renderPage('error404');
    }




}