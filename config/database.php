<?php

/**
 * Database connection configs.
 * Default is current selected dsn.
 */
return [
    'default' => 'mysql',
    'mysql' => [
        'host' => 'localhost',
        'name' => 'dbname',
        'user' => 'dbuser',
        'password' => 'dbpassword'
    ],
    'pgsql' => [],
    'mssql' => [],
    'sqlite' => []
];