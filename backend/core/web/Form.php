<?php

namespace core\web;


/**
 * Class Form
 * @package core\base
 *
 *
 * Class used to generate form with fields and buttons
 *
 * Example below is used in login page view  that can be seen at http://site.com/login/
 *
 * $loginForm = new Form(['method'=>'POST', 'class'=>'form-signin']);
 * <?=$loginForm->begin()?>
 * <h2 class="form-signin-heading">Authorization</h2>
 * <?=$loginForm->field('text', ['name'=>'login', 'class'=>'form-control', 'id'=>'inputMail', 'placeholder'=>'your login', 'required'=>true, 'autofocus'=>'true'])?>
 * <?=$loginForm->field('password', ['name'=>'password', 'id'=>'inputPassword', 'class'=>'form-control', 'placeholder'=>'*******', 'required'=>true])?>
 * <?=$loginForm->field('submit', ['name'=>'log_in', 'class'=>'btn btn-lg btn-primary btn-block', 'value'=>'Login'])?>
 * <?=$loginForm->end()?>
 *
 */

class Form {

    private $formStart; //Form beginning content
    private $field; //current customizing form field
    private $method;
    private $action;
    private $class;
    private $id;
    private $baseFieldParams = ['name', 'value', 'placeholder', 'id', 'class'];
    private $submitted = false;




    public function __construct($params)
    {
        $this->method = isset($params['method']) && strtolower($params['method']) == 'get' ? 'GET' : 'POST';
        $this->action = isset($params['action']) ? $params['action'] : null;
        $this->class = isset($params['class']) ? $params['class'] : null;
        $this->id = isset($params['id']) ? $params['id'] : null;

        $this->checkAlreadySubmitted();


        $this->preBuild();

    }


    /**
     *  void method. sets $this->submitted to true if form method is in server request_method
     */
    private function checkAlreadySubmitted()
    {
        if ($this->method == "POST" && !empty($_POST)){
            $this->submitted = true;
        }

        if($this->method == "GET" && !empty($_GET)){
            $this->submitted = true;
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


    /**
     * Generates form beginning tag and attributes
     */
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


    /**
     * @param string $type form field type
     * @param array $params attributes of field ( class, id, value, placeholder and etc.)
     * @return mixed generated field html code
     */
    public function field($type, $params)
    {

        if(!empty($params['name'])){

            $params['value'] = $this->fillWithSubmittedValue($params['name']);

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


    /**
     * @param array $params attributes of field ( class, id, value, placeholder and etc.)
     */
    protected function fieldText($params)
    {


        $params['type'] = 'text';

        $this->field = '<input';

        $this->writeFieldParams($params);

        $this->field .= '>';


    }


    /**
     * @param array $params attributes of field ( class, id, value, placeholder and etc.)
     */
    protected function fieldPassword($params)
    {

        $params['type'] = 'password';

        $this->field = '<input';

        $this->writeFieldParams($params);

        $this->field .= '>';

    }




    /**
     * @param array $params attributes of field ( class, id, value, placeholder and etc.)
     */
    protected function fieldEmail($params)
    {

        $params['type'] = 'email';

        $this->field = '<input';

        $this->writeFieldParams($params);

        $this->field .= '>';

    }



    /**
     * @param array $params attributes of field ( class, id, value, placeholder and etc.)
     */
    protected function fieldNumber($params)
    {


        $params['type'] = 'number';

        $this->field = '<input';

        $this->writeFieldParams($params);

        $this->field .= '>';


    }




    /**
     * @param array $params attributes of field ( class, id, value, placeholder and etc.)
     */
    protected function fieldTextArea($params)
    {

        $value = $params['value'];
        $params['value'] = null;


        $this->field = '<textarea';

        $this->writeFieldParams($params);

        $this->field .= '>' . Html::encode($value);

        $this->field .= '</textarea>';


    }



    /**
     * @param array $params attributes of field ( class, id, value, placeholder and etc.)
     */
    protected function fieldSubmit($params)
    {
        $params['type'] = 'submit';

        $this->field = '<input';

        $this->writeFieldParams($params);

        $this->field .= '>';

    }


    /**
     * @param array $params attributes of field ( class, id, value, placeholder and etc.)
     * @return mixed sanitized params for field. FOr example ['onclick'=>'someAction', 'class'=>'myClass']
     * will return ['class'=>'myClass'] because onclick param is not in $this->baseFieldParams
     */
    protected function fillBaseFieldParams($params)
    {
        foreach($this->baseFieldParams as $param){
            if(!isset($params[$param])){
                $params[$param] = null;
            }
        }

        return $params;

    }


    /**
     * @param array $params attributes of field ( class, id, value, placeholder and etc.)
     * Fills field with attributes passed
     * $params = ['class'=>'someClass', 'placeholder'=>'someText']; will return class="someClass" placeholder="someText"
     */
    protected function writeFieldParams($params)
    {

        foreach($params as $key=>$value){
            if($value != null){
                $this->field .= ' ' . Html::encode($key) . '="' . Html::encode($value) . '"';
            }
        }
    }


    /**
     * If form method initialized tries to get method[field] value and return it
     * Elsewhere returns null
     *
     * @param $fieldName
     * @return null|string
     */
    protected function fillWithSubmittedValue($fieldName)
    {
        if($this->submitted){
            if(!empty($_POST[$fieldName])){
                return Html::encode($_POST[$fieldName]);
            }
        }

        return null;
    }


    /**
     * @return string prints fully generated form field
     */
    protected function showField()
    {
        $field = $this->field;
        $this->field = '';
        return $field;
    }





} 