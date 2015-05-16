<?php
/**
 * Created by PhpStorm.
 * User: temirkhan
 * Date: 16.04.15
 * Time: 23:02
 */

date_default_timezone_set("Europe/Moscow");

return  [

    'debugMode' =>true,

    'language' => 'ru',

    'database' => [
        'host' => 'localhost',
        'name' => 'myProject',
        'user' => 'root',
        'password' => ''
    ],

    'plugins' => [
        'localization',
        'bootstrap',
        'password-compat' // used for function  "password_(verify|hash...)" compatibility  in PHP 5.4-5.5
    ],

    'routes' => [
        ['route' => '~^/*$~', 'action' => 'main/index'],

        ['route' => '~^/admin/add_question$~', 'action' => 'admin/add-question',],

        ['route' => '~^/api/get_question$~', 'action' => 'api/get-question',],

        ['route' =>'~^/login$~', 'action' => 'user/login'],

        ['route' =>'~^/profile$~', 'action' => 'user/profile'],

        ['route' =>'~^/logout$~', 'action' => 'user/logout'],

        ['route' => '~^/showcase$~', 'action' => 'main/example'],

        'error_404'=>[
            'action' => 'main/error404',
        ]
    ]
];