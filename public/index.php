<?php

define('ROOT_DIR', realpath($_SERVER['DOCUMENT_ROOT']. '/..'));
define('FRONTEND_DIR', $_SERVER['DOCUMENT_ROOT']);
define('TEMPLATE_DIR', ROOT_DIR . '/views/templates');
define('SITE_ADDRESS', $_SERVER['SERVER_NAME']);


require ROOT_DIR . '/backend/core/AutoLoader.php';
require ROOT_DIR . '/backend/core/App.php';


App::debugTrack();

App::init()->run();

App::debugInfo();