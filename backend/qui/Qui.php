<?php

use qui\app\AppDebug;
use qui\app\AppLog;
use qui\base\Controller;
use qui\db\Connection;
use qui\helper\FileSystem;
use qui\web\UrlManager;


/**
 * Class App
 *
 * Heart of web application.
 */
class Qui
{
    const DEFAULT_LANG = 'en';

    /**
     * @var Qui
     */
    public static $app; //contains the application instance

    /**
     * @var array
     */
    private static $config; //application configuration

    /**
     * database connections
     * @var array
     */
    private static $db;


    public static $language = self::DEFAULT_LANG;
    private static $request = [
        'get' => [], //$_GET
        'post' => [], //$_POST
        'cookie' => [], //$_COOKIE
        'routedParams' => [] // route params ruled in config and parsed by UrlManager
    ];


    /**
     * @return Qui
     */
    public static function init()
    {
        if (self::$app === null) {
            self::$config = self::getConfig();
            self::$app = new self();
        }
        return self::$app;
    }


    private function __construct()
    {
        AppDebug::switchDebugMode(self::getConfig('debugMode'));
    }


    /**
     * @param string $tracker if needed to track from different code points
     */
    public static function debugTrack($tracker = 'mainTracker')
    {
        if (self::getConfig('debugMode')) {
            AppDebug::debugTrack($tracker);
        }
    }


    /**
     * Output information about seconds past, memory peak and memory used
     *
     * @param string $tracker
     */
    public static function debugInfo($tracker = 'mainTracker')
    {
        if (self::getConfig('debugMode')) {
            AppDebug::debugInfo($tracker);
        }
    }


    /**
     * @param null $conf can be set to  'configName' or null
     * @return array|bool|null if arg passed as array returns ['configName'=>$confValue,..] else returns $confValue
     */
    public static function getConfig($conf = null)
    {
        if (self::$config === null) {
            self::$config = require ROOT_DIR . '/config/main.php';
        }

        if ($conf === null) {

            return self::$config;
        }
        
        return array_key_exists($conf, self::$config) ? self::$config[$conf] : null;
    }


    /**
     * @param string $param name of parameter
     * @param int $filter sanitizing rule
     * @return mixed|null
     */
    public function get($param, $filter = FILTER_SANITIZE_STRING)
    {
        return isset($_GET[$param]) ? filter_var($_GET[$param], $filter) : null;
    }


    /**
     * @param string $param name of parameter
     * @param int $filter sanitizing rule
     * @return mixed|null
     */
    public function post($param, $filter = FILTER_SANITIZE_STRING)
    {
        return isset($_POST[$param]) ? filter_var($_POST[$param], $filter) : null;
    }


    /**
     * @param string $param name of parameter
     * @param int $filter sanitizing rule
     * @return string|null
     */
    public function cookie($param, $filter = FILTER_SANITIZE_STRING)
    {
        return isset($_COOKIE[$param]) ? filter_var($_COOKIE[$param], $filter) : null;
    }


    /**
     * @param string $param name of parameter
     * @return null | string
     */
    public function routedParam($param)
    {
        return isset(self::$request['routedParams'][$param]) ? self::$request['routedParams'][$param] : null;
    }


    /**
     * Return DBMS operator that operates with declared database name
     *
     * Let it be you want manage database 'store' using postgres
     * $storeDBMS = App::$app->db('store', 'postgres');
     * and that's it you can operate it
     * $storeDBMS->get()->from('productTable')->where('id=123');
     *
     * There is no limits on connections and connection types
     * Application keeps connection instances in itself
     *
     * @param null | string $database required database name
     * @param null | string $dsn required database management system name
     *
     *
     * @return \qui\db\DBMSOperator | false
     */
    public function db($database = null, $dsn = null)
    {
        $dbConfig = self::getConfig('database') ? : [];

        $dsn = $dsn === null ? $dbConfig['default'] : $dsn;

        $database = $database === null ? $dbConfig[$dsn]['name'] : $database;

        $connectionConfig = $dbConfig[$dsn];
        $connectionConfig['name'] = $database;

        $connectionName = $dsn . '_' . $database;

        if (self::$db[$connectionName] === null) {
            self::$db[$connectionName] = Connection::open($dsn, $connectionConfig);
        }

        return self::$db[$connectionName] ? : false;
    }


    /**
     * Executes application.
     * Sanitizes and import superglobals(get, post, cookie) and route based params.
     * Initializes urlManager and Controller based on route config
     */
    public function run()
    {
        if (!AppLog::errorsExist()) {
            $urlManager = new UrlManager($_SERVER['REQUEST_URI'], self::getConfig('routes'));
            if ($urlManager->parseRequest()) {
                $route = $urlManager->getRoute();

                self::$request['routedParams'] = $urlManager->getRequestParams();
                Controller::runController($route);
            }
        }

        AppLog::showErrors();
    }
}