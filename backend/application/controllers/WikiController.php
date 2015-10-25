<?php

namespace application\controllers;


use core\base\Controller;

/**
 * This is a wiki pages controller. It is not necessary for correct application work.
 *
 * Class WikiController
 * @package application\controllers
 */
class WikiController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->view->setTitle('User guide - QUI');
        $this->view->setTemplate('wiki');
        $this->view->registerCss('/css/bootstrap/bootstrap.min.css');
        $this->view->registerCss('/js/highlightjs/styles/monokai_sublime.css');
        $this->view->registerJs('/js/highlightjs/highlight.pack.js');
    }


    public function pageIndex()
    {
        $this->renderPage('index');
    }


    public function pageRouter()
    {
        $this->view->setTitle('Routing - QUI');
        $this->renderPage('router');
    }

    public function pageConfig()
    {
        $this->view->setTitle('Configurations - QUI');
        $this->renderPage('configuration');
    }

}