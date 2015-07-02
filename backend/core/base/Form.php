<?php
/**
 * Created by PhpStorm.
 * User: temirkhan
 * Date: 20.04.15
 * Time: 11:48
 */

namespace app\core\base;

use app\core\web\Html;


class Form {

    private $formStart;
    private $field;
    private $method;
    private $action;
    private $class;
    private $id;
    private $baseFieldParams;
    private $submited = false;




    public function __construct($params)
    {

        $this->baseFieldParams = ['name', 'value', 'placeholder', 'id', 'class'];

        $this->method = isset($params['method']) && strtolower($params['method']) == 'get' ? 'GET' : 'POST';
        $this->action = isset($params['action']) ? $params['action'] : null;
        $this->class = isset($params['class']) ? $params['class'] : null;
        $this->id = isset($params['id']) ? $params['id'] : null;

        $this->checkAlreadySubmited();


        $this->preBuild();

    }


    private function checkAlreadySubmited()
    {
        if ($this->method == "POST" && !empty($_POST)){
            $this->submited = true;
        } elseif($this->method == "GET" && !empty($_GET)){
            $this->submited = true;
        }
    }


    public function begin()
    {
        return $this->formStart . PHP_EOL;
    }

    public function end()
    {
        return '</form>' . PHP_EOL;
    }



    protected function preBuild()
    {


        $this->formStart = '<form';


        if($this->class != null){
            $this->formStart .= ' class="' . Html::encode($this->class) . '"';
        }

        if($this->id != null){
            $this->formStart .= ' id="' . Html::encode($this->id) . '"';
        }


        $this->formStart .= ' method="' . $this->method . '"';


        if($this->action != null){
            $this->formStart .= ' action="' . Html::encode($this->action) . '"';
        }


        $this->formStart .= '>';


    }



    public function field($type, $params)
    {

        if(!empty($params['name'])){

            $params['value'] = $this->fillWithSubmitedValue($params['name']);

        }

        $params = $this->fillBaseFieldParams($params);

        switch($type){

            case 'text':
                $this->fieldText($params);
                break;

            case 'number':
                $this->fieldNumber($params);
                break;

            case 'password':
                $this->fieldPassword($params);
                break;

            case 'email':
                $this->fieldEmail($params);
                break;


            case 'select':
                //
                break;

            case 'textarea':
                $this->fieldTextArea($params);
                break;

            case 'submit':
                $this->fieldSubmit($params);
                break;

            default:
                //
                break;

        }

        return $this->showField();


    }


    protected function fieldText($params)
    {


        $params['type'] = 'text';

        $this->field = '<input';

        $this->writeFieldParams($params);

        $this->field .= '>';


    }


    protected function fieldPassword($params)
    {

        $params['type'] = 'password';

        $this->field = '<input';

        $this->writeFieldParams($params);

        $this->field .= '>';

    }



    protected function fieldEmail($params)
    {

        $params['type'] = 'email';

        $this->field = '<input';

        $this->writeFieldParams($params);

        $this->field .= '>';

    }



    protected function fieldNumber($params)
    {


        $params['type'] = 'number';

        $this->field = '<input';

        $this->writeFieldParams($params);

        $this->field .= '>';


    }


    protected function fieldTextArea($params)
    {

        $value = $params['value'];
        $params['value'] = null;


        $this->field = '<textarea';

        $this->writeFieldParams($params);

        $this->field .= '>' . Html::encode($value);

        $this->field .= '</textarea>';


    }


    protected function fieldSubmit($params)
    {
        $params['type'] = 'submit';

        $this->field = '<input';

        $this->writeFieldParams($params);

        $this->field .= '>';

    }




    protected function fillBaseFieldParams($params)
    {
        foreach($this->baseFieldParams as $param){
            if(!isset($params[$param])){
                $params[$param] = null;
            }
        }

        return $params;

    }


    protected function writeFieldParams($params)
    {

        foreach($params as $key=>$value){
            if($value != null){
                $this->field .= ' ' . Html::encode($key) . '="' . Html::encode($value) . '"';
            }
        }
    }


    protected function fillWithSubmitedValue($fieldName)
    {
        if($this->submited){
            if(!empty($_POST[$fieldName])){
                return Html::encode($_POST[$fieldName]);
            }
        }

        return null;
    }





    protected function showField()
    {
        $field = $this->field;
        $this->field = '';
        return $field;
    }





} 