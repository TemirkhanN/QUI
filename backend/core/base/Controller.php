<?php

namespace core\base;


use core\app\AppLog;

/**
 * Class Controller
 * @package core\base
 *
 *
 * Mostly classes logic protected and not available for child classes that you will create at /backend/controllers/
 */
class Controller
{


    /**
     * @var View
     */
    private $view;

    private $pageMethodPrefix = 'page'; // prefix for all view methods in controller



    public function __construct()
    {
    }



    public static function runController($route)
    {

        if (strpos($route, '/')) {
            list($controllerName, $page) = explode('/', $route);
        } else {
            list($controllerName, $page) = [$route, 'index'];
        }

        while ($controllerInSubDir = strpos($controllerName, '-')) {
            $leastString = mb_strcut($controllerName, $controllerInSubDir + 1); //What passed after matching - . At least shall stay only real controller name
        }

        if (!empty($leastString)) {
            $controllerName = str_replace($leastString, ucfirst($leastString), $controllerName);
        } else {
            $controllerName = ucfirst($controllerName);
        }


        $cName = '\\application\\controllers\\' . $controllerName . 'Controller';

        /**
         * Initializing necessary controller
         * @var Controller $controller
         *
         */
        $controller = new $cName();
        $controller->run($page);
    }

    /**
     * Initializes view and tries to execute
     *
     * @param string $page default index
     */
    private function run($page)
    {
        $this->view = new View();
        $this->view->setPage($page);

        try {
            if (method_exists($this, $this->pageMethodPrefix . $page)) {
                $this->{$this->pageMethodPrefix . $page}();
            } else{
                throw new \Exception('Method '.$page.' does not exist in controller ' . (new \ReflectionClass($this))->getName());
            }
        } catch(\Exception $error){
            AppLog::noteError($error);
        }

    }


    /**
     * @param string $page rendering view-page name
     * @param array $variables extracting variables that will be locally visible in rendering page-view
     * @param string $template name of template that shall be rendered
     * @param string $type extension of "rendering" file
     */
    protected function renderPage($page, $variables = [], $template = 'default', $type = 'php')
    {
        $className = preg_replace('#Controller$#', '', (new \ReflectionClass($this))->getShortName());
        /* replaces camelCase by camel-case */
        $className = strtolower(preg_replace('#([A-Z]{1})#', '-${1}', lcfirst($className)));
        $pageFile = ROOT_DIR . '/views/pages/' . $className . '/' . $page . '.' . $type;

        try {
            $this->view->render($pageFile, $variables, $template, $type);
        } catch(\Exception $error){
            AppLog::noteError($error);
        }
    }


    /**
     * Set title to loaded view
     *
     * @param string $title
     */
    protected function setTitle($title)
    {
        $this->view->setTitle($title);
    }


    /**
     * @param string $url where redirection shall happen
     * it may be absolute url or relative path to local resource
     * For example: $this->redirect('/login/'); $this->redirect('https://github.com');
     *
     * @param int $code header answer code while redirecting
     */
    public function redirectToUrl($url = '', $code = 303)
    {
        if(!preg_match("~^http(s?)://~", $url)) {
            header("Location:" . $url, true, $code);
        } elseif(filter_var($url, FILTER_VALIDATE_URL)){
            header("Location:/redirect.php?url=".$url, true, $code);
        }
        exit();
    }


    /**
     * @param string $request  redirects from current controller to another one
     * for example myController/action  or  myController
     */
    public function redirect($request = '')
    {
        self::runController($request);
    }
}