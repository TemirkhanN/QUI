<?php

namespace application\controllers;


use qui\base\Controller;

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
        $this->view->bindCss('/css/bootstrap/bootstrap.min.css');
        $this->view->bindCss('/js/highlightjs/styles/monokai_sublime.css');
        $this->view->bindJs('/js/highlightjs/highlight.pack.js');
    }


    public function pageIndex()
    {
        $content = $this->renderPartial('wiki/index');
        $this->view->setContent($content);

        $this->renderTemplate('wiki');
    }


    public function pageRouter()
    {
        $content = $this->renderPartial('wiki/router');
        $this->view->setTitle('Routing - QUI');
        $this->view->setContent($content);
        $this->renderTemplate('wiki');
    }


    public function pageConfig()
    {
        $this->view->setTitle('Configurations - QUI');
        $this->view->setContent($this->renderPartial('wiki/configuration'));
        $this->renderTemplate('wiki');
    }

}