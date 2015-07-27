<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 18.06.2015
 * Time: 10:03
 */

namespace app\controllers;


use app\core\App;
use app\core\base\Controller;
use app\plugins\sb_admin\SbAdmin;

class SbAdminController extends Controller
{


    

    public function pageIndex()
    {
        App::switchOffDebug();
        SbAdmin::loadDependencies();
        $this->setCustomTemplate(true);
        $this->renderPage('index');
    }


    public function pageCharts()
    {
        App::switchOffDebug();
        SbAdmin::loadDependencies();
        $this->setCustomTemplate(true);
        $this->renderPage('charts');
    }


    public function pageForms()
    {
        App::switchOffDebug();
        SbAdmin::loadDependencies();
        $this->setCustomTemplate(true);
        $this->renderPage('forms');
    }

    public function pageTables()
    {
        App::switchOffDebug();
        SbAdmin::loadDependencies();
        $this->setCustomTemplate(true);
        $this->renderPage('tables');
    }



    public function pageContent()
    {
        App::switchOffDebug();
        SbAdmin::loadDependencies();
        $this->setCustomTemplate(true);
        $this->renderPage('content');
    }

    public function pageGrids()
    {
        App::switchOffDebug();
        SbAdmin::loadDependencies();
        $this->setCustomTemplate(true);
        $this->renderPage('grids');
    }


    public function pageBlank()
    {
        App::switchOffDebug();
        SbAdmin::loadDependencies();
        $this->setCustomTemplate(true);
        $this->renderPage('blank');
    }


    public function pageBootstrap()
    {
        App::switchOffDebug();
        SbAdmin::loadDependencies();
        $this->setCustomTemplate(true);
        $this->renderPage('bootstrap');
    }


}