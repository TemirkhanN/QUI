<?php

namespace qui\base;
use qui\app\AppLog;


/**
 * Class Controller
 * @package qui\base
 *
 *
 * Mostly classes logic protected and not available for child classes that you will create at /backend/controllers/
 */
class Controller
{
    /**
     * @var View
     */
    protected $view;
    
    /**
     * @var string
     */
    private $pageMethodPrefix = 'page'; // prefix for all view methods in controller

    
    public function __construct()
    {
        $this->view = new View();
    }


    /**
     * @param string $template
     * @param array $variables extracting variables that will be locally visible in rendering page-view
     */
    protected function renderTemplate($template = '', array $variables = [])
    {
        try {
            echo $this->view->render(ROOT_DIR . '/views/templates/' . $template, $variables);
        } catch (\InvalidArgumentException $e){
            AppLog::noteError($e);
        }
    }

    /**
     * @param string $partial
     * @param array $variables extracting variables that will be locally visible in rendering page-view
     * 
     * @return string rendered content
     */
    protected function renderPartial($partial = '', array $variables = [])
    {
        $content = '';
        
        try {
            $content = $this->view->render(ROOT_DIR . '/views/pages/' . $partial, $variables);
        } catch (\InvalidArgumentException $e){
            AppLog::noteError($e);
        }
        
        return $content;
    }

    /**
     * @param $route
     */
    public static function runController($route)
    {
        if (strpos($route, '/')) {
            list($controllerName, $page) = explode('/', $route);
        } else {
            list($controllerName, $page) = [$route, 'index'];
        }

        $tmpControllerName = $controllerName;
        while ($controllerInSubDir = strpos($tmpControllerName, '-')) {
            $leastString       = mb_strcut($controllerName, $controllerInSubDir + 1); //What passed after matching - . At least shall stay only real controller name
            $tmpControllerName = $leastString;
        }

        if (!empty($leastString)) {
            $controllerName = str_replace([$leastString, '-'], [ucfirst($leastString), '\\'], $controllerName);
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
        if (method_exists($this, $this->pageMethodPrefix . $page)) {
            $this->{$this->pageMethodPrefix . $page}();
        } else {
            $this->redirect('main/error404');
        }
    }

    /**
     * Default page rendered for error page
     */
    public function pageError404()
    {
        $this->view->setTitle('Not Found');
        $content = $this->renderPartial('error-pages/error404');
        $this->view->setContent($content);
        $this->renderTemplate('empty');
    }


    /**
     * @param string $url where redirection shall happen
     * it may be absolute url or relative path to local resource
     * For example: $this->redirectToUrl('/login/'); $this->redirectToUrl('https://github.com');
     *
     * @param int $code header answer code while redirecting
     * @param bool $interrupt shall script execution stop or not
     */
    public function redirectToUrl($url = '', $code = 303, $interrupt = true)
    {
        if (!preg_match('~^http(s?)://~', $url)) {
            header('Location:' . $url, true, $code);
        } elseif (filter_var($url, FILTER_VALIDATE_URL)) {
            header('Location:/redirect?url=' . $url, true, $code);
        }

        if ($interrupt) {
            exit();
        }
    }


    /**
     * @param string $request redirects from current controller to another one
     * for example myController/action  or  myController
     * Note! Url adress will stay same. It doesn't redirect physically
     * For physical redirect use redirectToUrl
     * @param bool $interrupt shall script execution stop or not
     */
    public function redirect($request = '', $interrupt = true)
    {
        self::runController($request);
        if ($interrupt) {
            exit();
        }
    }
}