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

    'database' => [
        'host' => 'localhost',
        'name' => 'myProject',
        'user' => 'root',
        'password' => ''
    ],

    'plugins' => [
        'bootstrap',
        'password_compat' // Используется для поддержки функций password_+  в PHP 5.3-5.5
    ],

    'routes' => [
        [
            'route' => '~^/*$~',
            'action' => 'main/index'
        ],

        [
            'route' => '~^/admin/add_question$~',
            'action' => 'admin/add-question',
        ],


        [
            'route' => '~^/api/get_question$~',
            'action' => 'api/get-question',
        ],

        'error_404'=>[
            'action' => 'main/error404',
        ]
    ]
];