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

    protected $route = null;




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

        $request = parse_url($this->request);

        foreach ($this->rules as $key=>$rule) {
            if (is_numeric($key) && preg_match($rule['route'], $request['path'])){

                $this->route = $rule;
                break;
            }

        }

        if($this->route === null){
            $this->route = $this->rules['error_404'];
        }

    }



    public function getRoute()
    {
        return $this->route;
    }


}