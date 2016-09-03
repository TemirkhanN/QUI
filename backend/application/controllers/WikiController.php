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

        $this->setTitle('User guide - QUI');
        $this->bindCss('/css/bootstrap/bootstrap.min.css');
        $this->bindCss('/js/highlightjs/styles/monokai_sublime.css');
        $this->bindJs('/js/highlightjs/highlight.pack.js');
    }


    public function pageIndex()
    {
        $content = $this->renderPartial('wiki/index');
        $this->setContent($content);

        $this->renderTemplate('wiki');
    }


    public function pageRouter()
    {
        $content = $this->renderPartial('wiki/router');
        $this->setTitle('Routing - QUI');
        $this->setContent($content);
        $this->renderTemplate('wiki');
    }


    public function pageConfig()
    {
        $this->setTitle('Configurations - QUI');
        $this->setContent($this->renderPartial('wiki/configuration'));
        $this->renderTemplate('wiki');
    }

}