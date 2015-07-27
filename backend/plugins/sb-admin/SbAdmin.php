<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 18.06.2015
 * Time: 10:03
 */

namespace app\plugins\sb_admin;


use app\core\App;
use app\core\base\Plugin;
use app\core\web\UrlManager;

class SbAdmin implements  Plugin
{


    private static $rightSideMenu = false; //menu position. if true menu will be on right side


    //This add-on has too much css and js files that need to be included only on admin page. So dependencies will be included only from controller
    private static $dependencies = [
        'css' => ['bootstrap.min',
            'font-awesome.min',
            'sb-admin',
            'morris',
        ],

        'js' => ['bootstrap.min',
            'flot/excanvas.min',
            'flot/flot-data',
            'flot/jquery.flot',
            'flot/jquery.flot.pie',
            'flot/jquery.flot.resize',
            'flot/jquery.flot.tooltip.min',
            'morris/morris.min',
            'morris/morris-data',
            'morris/raphael.min',
        ],

        'fonts' => ['glyphicons-halflings-regular.eot',
            'glyphicons-halflings-regular.svg',
            'glyphicons-halflings-regular.ttf',
            'glyphicons-halflings-regular.woff',
            'glyphicons-halflings-regular.woff2',
            'FontAwesome.otf',
            'fontawesome-webfont.eot',
            'fontawesome-webfont.svg',
            'fontawesome-webfont.ttf',
            'fontawesome-webfont.woff'
        ],
    ];




    public static function loadDependencies()
    {
        if(self::$rightSideMenu) {
            self::$dependencies['css'][] = 'sb-admin-rtl';
        }

        App::loadPluginDependencies('sb-admin', self::$dependencies);
    }


    public static function init()
    {
        $rules[] = ['route'=>'^/praefect/$', 'action'=>'sbAdmin/index'];
        $rules[] = ['route'=>'^/praefect/(\w+)/', 'full'=>true, 'action'=>'sbAdmin/{0}'];

        array_walk($rules, function($rule){
            UrlManager::addRule($rule);
        });
    }



}