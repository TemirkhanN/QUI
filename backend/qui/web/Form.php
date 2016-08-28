<?php

namespace qui\web;


/**
 * Class Form
 * @package qui\base
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

/**
 * Class Form
 * @package qui\web
 */
class Form
{
    /**
     * @var
     */
    private $field; //current customizing form field
    /**
     * @var string
     */
    private $method;
    /**
     * @var null
     */
    private $action;
    /**
     * @var null
     */
    private $class;
    /**
     * @var null
     */
    private $id;
    /**
     * @var array
     */
    private $baseFieldParams = ['name', 'value', 'placeholder', 'id', 'class'];
    /**
     * @var bool
     */
    private $submitted = false;


    /**
     * Form constructor.
     * @param $params
     */
    public function __construct($params)
    {
        $this->method = isset($params['method']) && strtolower($params['method']) === 'get' ? 'GET' : 'POST';
        $this->action = isset($params['action']) ? $params['action'] : null;
        $this->class  = isset($params['class']) ? $params['class'] : null;
        $this->id     = isset($params['id']) ? $params['id'] : null;

        $this->checkAlreadySubmitted();
    }


    /**
     *  void method. sets $this->submitted to true if form method is in server request_method
     */
    private function checkAlreadySubmitted()
    {
        $this->submitted = $this->method === 'POST' && count($_POST) ? : $this->method === 'GET' && count($_GET);
    }


    /**
     * @return string
     */
    public function begin()
    {
        $formContent = '<form';
        if ($this->class !== null) {
            $formContent .= ' class="' . Html::encode($this->class) . '"';
        }

        if ($this->id !== null) {
            $formContent .= ' id="' . Html::encode($this->id) . '"';
        }
        $formContent .= ' method="' . $this->method . '"';
        if ($this->action !== null) {
            $formContent .= ' action="' . Html::encode($this->action) . '"';
        }

        $formContent .= '>';

        return $formContent . PHP_EOL;
    }


    /**
     * @return string
     */
    public function end()
    {
        return '</form>' . PHP_EOL;
    }


    /**
     * @param string $type form field type
     * @param array $params attributes of field ( class, id, value, placeholder and etc.)
     * @return mixed generated field html code
     */
    public function field($type, $params, $defaultValue = null)
    {
        if (isset($params['name'], $params['value'])) {
            $params['value'] = $this->fillWithSubmittedValue($params['name']);
        }

        $params = $this->fillBaseFieldParams($params);

        switch ($type) {

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

        $value           = $params['value'];
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
        foreach ($this->baseFieldParams as $param) {
            if (!array_key_exists($param, $params)) {
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

        foreach ($params as $key => $value) {
            if ($value !== null) {
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
        if ($this->submitted && !empty($_POST[$fieldName])) {
            return Html::encode($_POST[$fieldName]);
        }

        return null;
    }


    /**
     * @return string prints fully generated form field
     */
    protected function showField()
    {
        $field       = $this->field;
        $this->field = '';
        return $field;
    }


}