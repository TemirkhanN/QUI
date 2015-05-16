<?php
/**
 * Created by PhpStorm.
 * User: temirkhan
 * Date: 16.04.15
 * Time: 20:11
 */

namespace app\core;



use app\core\web\Html;
use app\plugins\Localization\Localization;

class App {


    const DEFAULT_LANG = 'en';

    public static $app; //Contains the application object
    private static $config;
    public static $db;
    private static $dependencies;
    public static $plugins;
    public static $templateDir;
    public static $templateHeaders; // Contains js, css and other data that shall be included in application template
    private static $errors;
    private static $debug;
    public static $language = self::DEFAULT_LANG;





    private function __construct($config)
    {
        self::$config = $config;
        self::$templateDir = $_SERVER['DOCUMENT_ROOT']. '/templates';
        self::$db = new db\PdoWrapper(self::getConfig('database'));
        self::$language = self::getConfig('language');
    }



    public static function init($config)
    {
        if(self::$app == null){
            self::$app = new self($config);
        }

        return self::$app;
    }


    public static function debugTimeTrack($identifier = '', $force = false)
    {
        if(self::getConfig('debugMode') || $force){
            self::$debug['timeTracker'][$identifier] = microtime(true);
        }
    }


    public static function debugShowTimeTrack($identifier = '', $force = false)
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



    public static function debugInfo()
    {

        if(self::getConfig('debugMode')) {
            echo PHP_EOL . '<br>time wasted ' . App::debugShowTimeTrack('mainTracker') . ' sec';
            echo PHP_EOL . '<br>memory used ' . round(memory_get_usage() / 1024) . ' kb';
            echo PHP_EOL . '<br>memory peak ' . round(memory_get_peak_usage(true) / 1024) . ' kb';
        }
    }




    public function run()
    {

        if(self::$errors == null){
            if(self::$db->connectedSuccessfully()) {


                $this->loadPlugins(self::getConfig('plugins'));
                $this->loadModels(__DIR__ .'/../models');

                $route = (new web\UrlManager($_SERVER['REQUEST_URI'], self::getConfig('routes')))->getRoute();

                $this->runController($route['action']);
            }
        }

        self::showErrors();

    }





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




    public static function requireFile($filePath)
    {


        try {
            if (file_exists($filePath)) {

                return require_once $filePath;

            } else {

                throw new  \Exception('File ' . basename($filePath) . ' doesn\'t exist');

            }
        } catch (\Exception $error) {

            self::noteError($error);
            return false;

        }
    }



    protected static function loadPlugin($name = '')
    {
        try {
            $pluginFiles = self::requireFile(__DIR__ . '/../plugins/' . $name . '/plugin-dependencies.php');
            self::$plugins[$name] = true;

            if ($pluginFiles) {
                foreach ($pluginFiles as $type => $files) {
                    if ($type == 'php') {
                        foreach ($files as $fileName) {
                            self::requireFile(__DIR__ . '/../plugins/' . $name . '/' . $fileName . '.php');
                        }
                    } else {
                        foreach ($files as $fileName) {
                            $path = __DIR__ . '/../plugins/' . $name . '/' . $type . '/' . $fileName . '.' . $type;
                            self::$templateHeaders[$type][] = [
                                'path' => $path,
                                'type' => $type,
                                'fileName' => $fileName
                            ];
                        }

                    }

                }
            }
        } catch(\Exception $e){
            self::noteError($e);
        }
    }



    protected function loadPlugins($plugins)
    {
        if (is_array($plugins)){
            array_walk($plugins, 'self::loadPlugin');
        }
    }





    protected function loadModels($modelsDir = null)
    {
        $modelsDirs  = glob($modelsDir . '/*', GLOB_ONLYDIR);

        if (!empty($modelsDirs)){
            array_walk($modelsDirs, [$this, 'loadModels']);
        }

        $models = glob($modelsDir .'/*.php');
        array_walk($models, 'self::requireFile');
    }



    public static function getDependencies()
    {
        return self::$dependencies;
    }



    protected static function loadDependency($dependency, $type, $path)
    {

        if (self::requireFile(__DIR__ .'/' .$path . '.php') !== false){

            $type == null ? self::$dependencies[$dependency] = $path : self::$dependencies[$type][$dependency] = $path;
        }

    }




    public static function loadDependencies($dependencies, $type = null)
    {

        if (is_array($dependencies)){

            foreach ($dependencies as $dependency=>$path){

                is_array($path) ? self::loadDependencies($path, $dependency) : self::loadDependency($dependency, $type, $path);

            }
        }



    }



    public function runController($action = '')
    {

        list($controller, $page) = explode('/', $action);

        $controller = ucfirst($controller);
        self::requireFile(__DIR__ .'/../controllers/' . $controller . 'Controller.php');


        $cName = '\\app\\controllers\\' . $controller . 'Controller';

        $theme = self::$db->getRecord('parameter', 'fm_conf', ['name' => 'theme']);
        $siteName = self::$db->getRecord('parameter', 'fm_conf', ['name' => 'site_title']);

        if($theme && !empty($theme->parameter)){
            $theme = $theme->parameter;
        }

        if($siteName && !empty($siteName->parameter)) {
            $siteName = $siteName->parameter;
        }



        $controller = new $cName(self::$templateDir); // Initializing controller's template directory

        $controller->setTheme($theme);

        $controller->setPage($page);

        $controller->setTitle($siteName);

        $controller->run();

    }


    public static function copyToTemplateFolder($path, $fileType, $fileName)
    {
        $fileDir = self::$templateDir . '/' . $fileType;
        $fileShallExistPath = $fileDir .'/'. $fileName. '.' . $fileType;
        if(!file_exists($fileShallExistPath)){

            if(!is_dir($fileDir)){
                mkdir($fileDir, 0755);
            }
            copy($path, $fileShallExistPath);



        }

        $fileShallExistPath = str_ireplace($_SERVER['DOCUMENT_ROOT'], '', $fileShallExistPath);

        return $fileShallExistPath;

    }




    public static function noteError($error)
    {

        $trace = $error->getTrace();
        $loggedError['file'] = $trace[0]['file'];
        $loggedError['line'] = $trace[0]['line'];
        $loggedError['message'] = $error->getMessage();
        $loggedError['args'] = $trace[0]['args'];

        self::$errors[] = $loggedError;

    }



    protected static function showError($error)
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
        if(self::$errors !== null && self::getConfig('debugMode')) {
            if(isset(self::$plugins['bootstrap'])):?>

                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?=self::_t('error')?></h3>
                    </div>
                    <div class="panel-body">
                        <?array_walk(self::$errors, 'self::showError');?>
                    </div>
                </div>

            <? else: ?>
                ahaha
            <?endif;
        }

    }




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