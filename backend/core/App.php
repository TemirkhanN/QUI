<?php

namespace app\core;

use app\core\file_worker\File;
use app\core\web\Html;
use app\plugins\Localization\Localization;


/**
 * Class App
 * @package app\core
 *
 *
 * Heart of web application. All initialization, config, dependencies, models, plugins load flow here
 */
class App {


    const DEFAULT_LANG = 'en';

    public static  $app; //contains the application instance object
    private static $config; //application configuration
    public static  $db; //database connection
    private static $debug; //debug information(time and memory spending)
    private static $errors; // errors occurred during application run
    public static  $templateDir; //application template(view) folder
    public static  $templateHeaders; // contains js, css and other data that shall be included in application template header
    public static  $language = self::DEFAULT_LANG;
    public static  $request; //contains parsed from $_SERVER['REQUEST_URI'] parameters that ruled in config routes
    public static  $plugins; //list of loaded plugins
    public static  $protocol; //using transfer protocol(http || https)


    /**
     * @return object App
     */
    public static function init()
    {
        if(self::$app == null){
            self::$config = File::requireFile(ROOT_DIR . '/config/main.php');
            self::$app = new self();
        }

        return self::$app;
    }



    private function __construct()
    {
        session_start();
        self::$templateDir = FRONTEND_DIR. '/templates';
        self::$db = new db\PdoWrapper(self::getConfig('database'));
        if(self::$db->connectedSuccessfully() === false){
            return false;
        }
        self::$language = self::getConfig('language');
        self::$protocol = self::getConfig('transferProtocol') === 'https' ? 'https' : 'http';

        $this->loadPlugins(self::getConfig('plugins'));

        new \app\models\user\User(); //!!! temporarily here
    }


    /**
     * @param string $identifier if needed to track from different places
     * @param bool $force if needed debug even it turned off in config
     */
    public static function debugTrack($identifier = '', $force = false)
    {
        if(self::getConfig('debugMode') || $force){
            ini_set('display_errors', true);
            error_reporting(E_ALL & E_WARNING);
            self::$debug['timeTracker'][$identifier] = microtime(true);
        }
    }


    /**
     * @param string $identifier of needed to see tracker with identifier
     * @param bool $force to show debug even if it turned off
     * @return int|float if error occurred returns 999 seconds.
     */
    public static function debugShowTrack($identifier = '', $force = false)
    {
        if(self::getConfig('debugMode') || $force){

            try {
                if (isset(self::$debug['timeTracker'][$identifier])) {
                    return (microtime(true) - self::$debug['timeTracker'][$identifier]);
                } else{
                    throw new \Exception("This time tracker hasn't been initialized yet");
                }
            } catch(\Exception $error){
                self::noteError($error);
            }
        }

        return 999;
    }


    public static function switchOffDebug()
    {
        self::$config['debugMode'] = false;
    }



    public static function debugInfo()
    {

        if(self::getConfig('debugMode')) {
            echo PHP_EOL . '<br>time wasted ' . App::debugShowTrack('mainTracker') . ' sec';
            echo PHP_EOL . '<br>memory used ' . round(memory_get_usage() / 1024) . ' kb';
            echo PHP_EOL . '<br>memory peak ' . round(memory_get_peak_usage(true) / 1024) . ' kb';
        }
    }





    public function run()
    {

        if(self::$errors == null){
            if(self::$db->connectedSuccessfully()) {

                $urlManager = new web\UrlManager($_SERVER['REQUEST_URI'], self::getConfig('routes'));
                if($urlManager->parseRequest()) {

                    $route = $urlManager->getRoute();


                    self::$request['url'] = $urlManager->getRequestParams();
                    $this->fillRequestData();
                    $this->runController($route);
                }
            }
        }

        self::showErrors();

    }




    private function fillRequestData()
    {
        self::$request['cookies'] = &$_COOKIE;
        self::$request['get'] = &$_GET;
        self::$request['post'] = &$_POST;
    }




    public static function toCamelCase($word = '')
    {
        while($pos = strpos($word, '-')){
            $word = mb_substr($word, 0, $pos).ucfirst(substr($word, $pos+1));
        }

        return ucfirst($word);
    }


    /**
     * @param null $conf can be set to ['configName', 'anotherName',...] or 'configName'
     * @return array|bool|null if arg passed as array returns ['configName'=>$confValue,..] else returns $confValue
     */
    public static function getConfig($conf = null)
    {
        if (self::$config !== null) {

            if (!empty($conf)) {

                $new_arr = [];

                if(is_array($conf)) {
                    foreach ($conf as $key) {

                        if (isset(self::$config[$key])) {
                            $new_arr[$key] = self::$config[$key];
                        }

                    }
                } else{
                    $new_arr = isset(self::$config[$conf]) ? self::$config[$conf] : null;
                }

                return $new_arr;

            }

            return self::$config;
        }

        return false;
    }


    /**
     * @param array $plugins active plugins names that shall be included and activated in application
     */
    private function loadPlugins($plugins = [])
    {
        if (is_array($plugins)){
            foreach($plugins as $plugin){
                self::loadPlugin($plugin);
            }
        }
    }



    /**
     * requires and initializes plugin from /backend/plugins by folder name passed
     * if plugin has not dependencies file, method will try to load file with same name from folder
     * For example:
     * Plugin name: password_compat
     * has not plugin.dependencies in /backend/plugins/password-compat/
     * so it will try to load /backend/plugins/password-compat/PasswordCompat.php
     *
     * @param string $name for example 'bootstrap'
     */
    private static function loadPlugin($name = '')
    {
        try {
            if(file_exists(__DIR__ . '/../plugins/' . $name . '/plugin-dependencies.php')) {
                if(!isset(self::$plugins[$name])) {

                    $pluginFiles = File::requireFile(__DIR__ . '/../plugins/' . $name . '/plugin-dependencies.php');


                    self::$plugins[$name] = true;
                    self::loadPluginDependencies($name, $pluginFiles);


                } else{
                    throw new \Exception('Duplicate plugin include for  "'. $name .'"');
                }


            } elseif(file_exists(__DIR__ . '/../plugins/' . $name . '/' . self::toCamelCase($name).'.php')){
                File::requireFile(__DIR__ . '/../plugins/' . $name . '/' . self::toCamelCase($name).'.php');
            }

            self::initializePlugin($name);

        } catch(\Exception $e){
            self::noteError($e);
        }
    }


    /**
     * @param string $name plugin name
     * @param array $pluginFiles files passed by array ['css'=>['someFile',...], 'js'=>['someFile',..], 'fonts'=>['someFont.ttf',..]]
     */
    public static function loadPluginDependencies($name = '', $pluginFiles = [])
    {
        if (!empty($pluginFiles) && is_array($pluginFiles)) {
            foreach ($pluginFiles as $type => $files) {

                switch($type){
                    case 'php':
                        foreach ($files as $fileName) {
                            File::requireFile(__DIR__ . '/../plugins/' . $name . '/' . $fileName . '.php');
                        }
                        break;

                    case 'css':
                    case 'js':
                        foreach ($files as $fileName) {
                            $path = __DIR__ . '/../plugins/' . $name . '/' . $type . '/' . $fileName . '.' . $type;
                            self::$templateHeaders[$type][] = [
                                'path' => $path,
                                'type' => $type,
                                'fileName' => $fileName,
                                'folder' => $name
                            ];
                        }
                        break;

                    case 'fonts':
                        foreach ($files as $fileName) {
                            $path = __DIR__ . '/../plugins/' . $name . '/' . $type . '/' . $fileName;
                            self::$templateHeaders[$type][] = [
                                'path' => $path,
                                'type' => substr($fileName, strpos($fileName, '.')+1),
                                'fileName' => substr($fileName, 0, strpos($fileName, '.')),
                                'folder' => 'fonts',
                            ];
                        }
                        break;

                    default:

                        break;
                }

            }
        }
    }





    private static function initializePlugin($pluginName = '')
    {
        $pluginClass = str_replace('-', '_', 'app\plugins\\' . $pluginName .'\\'. self::toCamelCase($pluginName));

        if(class_exists($pluginClass) && method_exists($pluginClass, 'init')){
            $pluginClass::init();
        }

    }



    public static function pluginIsActive($pluginName = '')
    {
        return isset(self::$plugins[$pluginName]) && self::$plugins[$pluginName];
    }



    /**
     * @param string $action contains controllerName || ControllerName/actionName arg.  'ControllerName/actionName'
     * if only controllerName set it will require index action that equals to ControllerClass->pageIndex()
     */
    public function runController($action = '')
    {

        if(strpos($action, '/')) {
            list($controller, $page) = explode('/', $action);
        } else{
            list($controller, $page) = [$action, 'index'];
        }

        while($controllerInSubDir = strpos($controller,'-')){
            $leastString = mb_strcut($controller, $controllerInSubDir+1); //What passed after matching - . At least shall stay only real controller name
            $controller = mb_strcut($controller, 0, $controllerInSubDir) . '\\' . $leastString;
        }

        if(!empty($leastString)){
            $controller = str_replace($leastString, ucfirst($leastString), $controller);
        } else{
            $controller = ucfirst($controller);
        }


        $cName = '\\app\\controllers\\' . $controller . 'Controller';




        if(self::$db->checkTableExist('fm_conf') === false){
            self::$db->query("CREATE TABLE IF NOT EXISTS `fm_conf` (
                                  `id` INT(11) AUTO_INCREMENT,
                                  `name` VARCHAR (50) NOT NULL DEFAULT '',
                                  `parameter` VARCHAR(100) NOT NULL DEFAULT '',
                                  PRIMARY KEY (`id`),
                                  UNIQUE (`name`)
                              ) ENGINE=InnoDB");
            $theme = null;
            $siteName = "Set default sitename in `fm_conf`";
        } else{
            $theme = self::$db->getRecord('parameter', 'fm_conf', ['name' => 'theme']);
            $siteName = self::$db->getRecord('parameter', 'fm_conf', ['name' => 'site_title']);
        }

        if($theme && !empty($theme->parameter)){
            $theme = $theme->parameter;
        }

        if($siteName && !empty($siteName->parameter)) {
            $siteName = $siteName->parameter;
        }

        $controller = new $cName(self::$templateDir); // Initializing controller's template directory

        $controller->run($theme, $page, $siteName);

    }





    /**
     * @param \Exception $error information
     */
    public static function noteError($error)
    {

        $trace = $error->getTrace();
        $loggedError['file'] = $trace[0]['file'];
        $loggedError['line'] = $trace[0]['line'];
        $loggedError['message'] = $error->getMessage();
        $loggedError['args'] = $trace[0]['args'];

        self::$errors[] = $loggedError;

    }



    protected static function showError($error = [])
    {

        echo 'Error occurred in ' .$error['file'] . ' on line ' . $error['line'] . '<br>';
        echo  $error['message'];

        echo '<br> at ';
        foreach ($error['args'] as $arg) {
            if(!is_array($arg)){
                echo  $arg .' ';
            }
        }

        echo  '<br><br>';

    }


    public static function showErrors()
    {
        if(self::$errors !== null && self::getConfig('debugMode')):?>
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?=self::t('error')?></h3>
                    </div>
                    <div class="panel-body">
                        <?array_walk(self::$errors, 'self::showError');?>
                    </div>
                </div>
        <? endif;

    }


    /**
     * @param string $text translating text
     * @param string $lang language. en, ru
     * @return string
     */
    public static function t($text = '', $lang = '')
    {
        if(empty($lang)){
            $lang = self::$language;
        }

        $words = explode(' ', $text);

        if(isset(self::$plugins['localization'])){
            $text = '';
            foreach($words as $word){
                $text .= ' ' . Localization::translate($word, $lang);
            }
        }

        return Html::encode($text);
    }



}