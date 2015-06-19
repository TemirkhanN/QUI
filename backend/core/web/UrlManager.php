<?php
/**
 * Created by PhpStorm.
 * User: temirkhan
 * Date: 17.04.15
 * Time: 7:27
 */

namespace app\core\web;


use app\core\App;

class UrlManager {


    protected $request = ['path'=>'', 'full'=>'']; //current request_uri info

    protected static $rules = []; // matching rules. By default from /config/main.php

    protected $actionRoute = null; //detecting action that will be passed to App and called from controller

    protected $params = []; //params passed through rules




    public function __construct($request, $rules)
    {
        if($request && $rules && is_array($rules)) {
            $this->request['full'] = $request;
            $this->request['path'] = parse_url($request, PHP_URL_PATH);




                foreach ($rules as $key => $rule) {
                    try {
                        if (self::validateRule($rule) == false) {
                            unset($rules[$key]);
                            throw new \Exception('Bad route rule passed to UrlManager');
                        }
                    } catch(\Exception $error) {
                        App::noteError($error);
                    }
                }
            self::$rules = array_merge(self::$rules, $rules);
        }

    }



    public static function addRule($rule)
    {
        if(self::validateRule($rule)) {
            self::$rules[] = $rule;
        }
    }





    private static function validateRule($rule = [])
    {
        if(!is_array($rule) || empty($rule['route']) || empty($rule['action'])){
            return false;
        }

        return true;
    }






    public function parseRequest()
    {
        try {
            if (!empty(self::$rules) && !empty($this->request)) {

                foreach (self::$rules as $key => $rule) {
                    $request = isset($rule['full']) && $rule['full'] == true ? $this->request['full'] : $this->request['path'];
                    if (is_numeric($key) && preg_match('~'.$rule['route'].'~', $request, $match)) {
                        $this->detectActionRoute($rule, $match);

                        if (!empty($rule['params'])) {
                            $this->parseParams($rule['params'], $match);
                        }
                        break;
                    }

                }

                if ($this->actionRoute === null) {
                    $this->actionRoute = !empty(self::$rules['error_404']['action']) ? self::$rules['error_404']['action'] : 'main/index';
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



    private function detectActionRoute($rule = [], $match = [])
    {
        if(count($match)>1 && empty($rule['params'])){
            preg_match('/{(\d+)}$/', $rule['action'], $action);
            array_shift($action);
            $action = $action[0];

           $rule['action'] = str_replace('{' . $action . '}', $match[$action+1], $rule['action']);
        }

        $this->actionRoute = $rule['action'];
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