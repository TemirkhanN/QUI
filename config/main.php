<?php

return  [
    'debugMode' => false,

    'local' => [
        'lang' => 'ru_RU',
        'timezone' => 'Europe/Moscow'
    ],

    'cache' => [
        'active' => true,
        'cacheTime' => 360,
    ],

    'database' => require(__DIR__.'/database.php'),

    'routes' => require(__DIR__.'/routes.php')
];