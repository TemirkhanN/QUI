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

    'transferProtocol'=>'http', // just for future to be sure not change links everywhere

    'language' => 'ru',

    'caching' => [
        'active' => true,
        'cacheTime' => 300, //in seconds
    ],

    'database' => [
        'host' => 'localhost',
        'name' => 'vforme',
        'user' => 'root',
        'password' => ''
    ],

    'plugins' => [
        'localization',
        'bootstrap',
        'password-compat', // used for function  "password_(verify|hash...)" compatibility  in PHP 5.4-5.5
        'rating',
        'translit',
        'pretty-date',
        'sb-admin',
    ],


    /** Route - RegEx that matches string in url address . Required!
     *  Action - controllerName/methodName that will be called if route matches url. Required!
     *  NOTE! method name can be set dynamically by defining between () in RegEx and passing that value so 'action'=>'controllerName/{param_order_index}
     *  Params - variables matched in route between () . params index shall match order in route.
     *  NOTE! for example: in route /(\d+)/(one|two)* second parameter(one|two) shall be defined like so 'params'=>[1=>'second_param']
     *  NOTE! params can be accessed from App::$request['param_name']
     *  Full - set true if should be checked full request url(not only PHP_URL_PATH)
    */

    'routes' => [
        ['route' => '^/?$', 'action' => 'main'],

        ['route' =>'^/login/$', 'action' => 'user/login'],

        ['route' =>'^/profile/$', 'action' => 'user/profile'],

        ['route' =>'^/logout/$', 'action' => 'user/logout'],

        ['route' => '^/showcase/$', 'action' => 'main/example'],

        ['route' => '^/(beauty+)/$', 'params'=>['category'], 'action' => 'article/show-articles'],

        ['route' => '^/beauty/([a-zA-Z0-9_]+)\.html$', 'params'=>['link'], 'action' => 'article/show-article'],

        ['route' => '^/API/articles.php\?([a-zA-Z_]+)', 'full'=>true, 'action' => 'api-content-apiArticle/{0}'],

        'error_404'=>[
            'route'=>'*',
            'action' => 'main/error404',
        ]
    ]
];