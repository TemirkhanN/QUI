<?php

use \app\core\App;

define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']. '/..');
define('SITE_ADDRESS', $_SERVER['SERVER_NAME']);

require ROOT_DIR . '/backend/core/App.php';
App::debugTrack('mainTracker', true);

require ROOT_DIR . '/backend/dependencies.php';

$config = App::requireFile(ROOT_DIR . '/config/main.php');

App::$app = App::init($config);

App::$app->run();

App::debugInfo();