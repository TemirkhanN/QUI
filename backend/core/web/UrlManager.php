<?php
/**
 * Created by PhpStorm.
 * User: temirkhan
 * Date: 17.04.15
 * Time: 7:27
 */

namespace app\core\web;


class UrlManager {


    protected $request;

    protected $rules;

    protected $actionRoute = null;

    protected $params = [];




    public function __construct($request, $rules)
    {
        if($request && $rules) {
            $this->request = $request;
            $this->rules = $rules;

            $this->parseRequest();
        }

    }






    public function parseRequest()
    {

        $request = $this->request;

        foreach ($this->rules as $key=>$rule) {
            if (is_numeric($key) && preg_match($rule['route'], $request, $match)){

                $this->detectActionRoute($rule, $match);

                if(!empty($rule['params'])){
                    $this->parseParams($rule['params'], $match);
                }
                break;
            }

        }

        if($this->actionRoute === null){
            $this->actionRoute = !empty($this->rules['error_404']['action']) ? $this->rules['error_404']['action'] : 'main/index';
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