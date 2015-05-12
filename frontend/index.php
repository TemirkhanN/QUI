<?php


define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']. '/..');

require ROOT_DIR . '/backend/core/Application.php';
require ROOT_DIR . '/backend/dependencies.php';

$config = app\core\Application::requireFile(ROOT_DIR . '/config/main.php');

(new app\core\Application($config))->run();