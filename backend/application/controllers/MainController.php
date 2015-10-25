<?php
namespace application\controllers;

use core\base\Controller;

class MainController extends Controller
{

    public function pageIndex()
    {
        $this->setTitle('Welcome to QUI');
        $this->renderPage('index', compact('var'));
    }
}