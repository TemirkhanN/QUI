<?php
namespace application\controllers;

use qui\base\Controller;

class MainController extends Controller
{

    public function pageIndex()
    {
        $this->view->setTitle('Welcome to QUI');
        $this->view->setContent($this->renderPartial('main/index'));

        $this->renderTemplate('default');
    }


    public function pageOutgoingLink()
    {
        $link = \Qui::$app->get('url');
        if($link && strpos($link, 'http') === 0){
            header('Location:' . $link, true, 303);
        } else{
            $this->redirect('/');
        }
    }
}