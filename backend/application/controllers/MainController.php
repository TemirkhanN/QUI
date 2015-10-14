<?php
namespace application\controllers;

use core\base\Controller;

class MainController extends Controller
{

    public function pageIndex()
    {
        $this->setTitle('Welcome to QUI');
        $var = 'Hello world';
        $this->renderPage('index', compact('var'));
    }


    public function pageExample()
    {
        $this->redirect('main/index');
    }


    public function pageError404()
    {
        $this->setTitle('Not Found');
        $this->renderPage('error404', null, 'empty');
    }


}