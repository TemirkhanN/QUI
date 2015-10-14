<?php

namespace core\web;


use core\App;

class UrlManager {


    protected $request = ['path'=>'', 'full'=>'']; //current request_uri info

    protected static $routes = []; // matching routes. By default from /config/main.php

    protected $actionRoute = null; //detecting action that will be passed to App and called from controller

    protected $params = []; //params passed through routes




    public function __construct($request, $routes)
    {
        if($request && $routes && is_array($routes)) {
            $this->request['full'] = $request;
            $this->request['path'] = parse_url($request, PHP_URL_PATH);




                foreach ($routes as $key => $route) {
                    try {
                        if (self::validRoute($route) == false) {
                            unset($routes[$key]);
                            throw new \Exception('Bad route route passed to UrlManager');
                        }
                    } catch(\Exception $error) {
                        App::noteError($error);
                    }
                }
            self::$routes = array_merge(self::$routes, $routes);
        }

    }



    public static function addRoute($route)
    {
        if(self::validRoute($route)) {
            self::$routes[] = $route;
        }
    }





    private static function validRoute($route = [])
    {

        return !(!is_array($route) || empty($route['route']) || empty($route['action']));
    }






    public function parseRequest()
    {
        try {
            if (!empty(self::$routes) && !empty($this->request)) {

                foreach (self::$routes as $key => $route) {
                    $request = isset($route['full']) && $route['full'] == true ? $this->request['full'] : $this->request['path'];
                    if (is_numeric($key) && preg_match('~'.$route['route'].'~', $request, $match)) {
                        $this->detectActionRoute($route, $match);

                        if (!empty($route['params'])) {
                            $this->parseParams($route['params'], $match);
                        }
                        break;
                    }

                }

                if ($this->actionRoute === null) {
                    $this->actionRoute = !empty(self::$routes['error_404']['action']) ? self::$routes['error_404']['action'] : 'main/index';
                }

                return true;

            } else{
                throw new \Exception('Invalid parameters passed to '.get_class($this));
            }
        } catch(\Exception $error){
            App::noteError($error);
            return false;
        }

    }



    private function parseParams($params = [], $matches = [])
    {
        array_shift($matches);  //Full match not needed for params
        if(!empty($matches) && !empty($params)){
            foreach($params as $key=>$index){
                if(!empty($index)) {
                    $this->params[$index] = isset($matches[$key]) ? $matches[$key] : null;
                }
            }

        }
    }



    private function detectActionRoute($route = [], $match = [])
    {
        if(count($match)>1 && empty($route['params'])){
            preg_match('/{(\d+)}$/', $route['action'], $action);
            array_shift($action);
            $action = $action[0];

           $route['action'] = str_replace('{' . $action . '}', $match[$action+1], $route['action']);
        }

        $this->actionRoute = $route['action'];
    }



    public function getRoute()
    {
        return $this->actionRoute;
    }



    public function getRequestParams()
    {
        return $this->params;
    }


}