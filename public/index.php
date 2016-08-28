<?php

define('FRONTEND_DIR', __DIR__);
define('ROOT_DIR', dirname(FRONTEND_DIR));
define('SITE_ADDRESS', $_SERVER['SERVER_NAME']);
define('DEV_MODE', true); //Switch off on production

if(DEV_MODE){
    ini_set('display_errors', 1);
    error_reporting(-1);
}


require ROOT_DIR . '/backend/qui/AutoLoader.php';
require ROOT_DIR . '/backend/qui/Qui.php';


Qui::debugTrack();

Qui::init()->run();

Qui::debugInfo();