<?php
use \app\core\App;

define('ROOT_DIR', realpath($_SERVER['DOCUMENT_ROOT']. '/..'));
define('FRONTEND_DIR', $_SERVER['DOCUMENT_ROOT']);
define('SITE_ADDRESS', $_SERVER['SERVER_NAME']);


require ROOT_DIR . '/backend/core/base/AutoLoader.php';
require ROOT_DIR . '/backend/core/App.php';


App::debugTrack('mainTracker', 'force');



App::init()->run();
App::debugInfo();