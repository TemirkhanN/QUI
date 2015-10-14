<?php

namespace core\base;

use core\app\AppLog;
use \core\helper\String;
use core\web\Html;

class View
{
    private $allowedViews = ['php', 'html', 'xhtml', 'xml'];
    private $headers = [];
    protected $layout = 'main/main-menu'; //default layout called as $this->layout();
    protected $page = 'index';
    protected $content = '';
    protected $customTemplate = false; //if set to true default.php won't be used (only rendering view)
    public $title = 'QUI framework'; //site title

    /**
     * @param string $page page name from view folder that will be rendered
     */
    public function setPage($page = 'index')
    {
        $page =  String::toCamelCase($page);
        $this->page = 'page'.ucfirst($page);
    }


    /**
     * @param string $description
     */
    public function setMetaDesc($description = '')
    {
        $this->setHeadMeta('<meta desc=" ' .Html::encode($description). '">');
    }


    /**
     * @param mixed string|array $tags
     */
    public function setMetaKeys($tags = '')
    {
        if(is_array($tags)){
            $tags = implode(',', $tags);
        }
        $this->setHeadMeta('<meta keywords=" ' .Html::encode($tags). '">');
    }


    /**
     * @param string $metaData is used to pass header data
     * For example $this->setHeadMeta('<script type="text'javascript" src="somejs.js">');
     */
    public function setHeadMeta($metaData = '')
    {
        static $tab = ''; //for careful tabs while watching source html code

        $this->headers[] = $tab.$metaData;

        if($tab === ''){
            $tab = '    ';
        }
    }


    /**
     * @param string $title setter pattern
     */
    public function setTitle($title = '')
    {
        $this->title = Html::encode($title);
    }




    public function render($pageFile, $variables = [], $template, $type)
    {
        if (in_array($type, $this->allowedViews)) {

            /* prevents replacing controller's non-system word. just to be sure it won't happen
             * !strongly recommended not to use such controller names(i.e. adminControllerController.
             */

            if(file_exists($pageFile)){

                $this->content = $this->renderContent($pageFile, $variables);
                $this->renderTemplate($template);

            } else{
                throw new \Exception('Page  "' . basename($pageFile) . '" doesn\'t exist in directory');
            }

        } else {
            throw new \Exception('Deprecated file type');
        }
    }


    /**
     * @param string $page
     * @param array $variables extracting variables that will be locally visible in rendering page-view
     * @return string rendered file
     */
    public function renderContent($page = '', $variables = [])
    {
        ob_start();

        if(!empty($variables)){
            extract($variables);
        }

        include $page;

        return ob_get_clean();
    }

    /**
     * Renders template file(layout) after content in it will be rendered and cleaned from buffer
     * @param string $template name of layout that shall be rendered and printed
     * @throws \Exception
     */
    private function renderTemplate($template)
    {
        $templatePath = TEMPLATE_DIR . '/'.$template.'.php';
        if(file_exists($templatePath)){
            include TEMPLATE_DIR . '/' . $template . '.php';
        } else{
            throw new \Exception('Template  "' . basename($templatePath) . '" doesn\'t exist in directory');
        }
    }


    /**
     * @param null $rawText raw text that will be sent to output as json
     * @param string $rawType type of rendering api answer. For now only json supported
     * if raw text is null,
     */
    public function renderApi($rawText = null, $rawType = 'json')
    {
        switch($rawType){
            case 'json':
                header('Content-Type: application/json; charset=utf-8');
                break;
            default:
                break;
        }

        if($rawText != null){
            echo $rawText;
        }
    }


    /**
     * @param string $path destination to including into headers js file
     * $this->registerJs('/js/rating/rating.js');
     *
     */
    public function registerJs($path = '')
    {
        $this->setHeadMeta('<script type="text/javascript" src="' . Html::encode($path) . '"></script>');
    }


    /**
     * @param string $path destination to including into headers css file
     * $this->registerCss('/css/bootstrap/bootstrap.min.css');
     */
    public function registerCss($path = '')
    {
        $this->setHeadMeta('<link rel="stylesheet" type="text/css" href="' . Html::encode($path) . '">');
    }


    /**
     * @return string prints headers of application
     */
    public function headers()
    {
        $headers = '';

        foreach($this->headers as $header){

            $headers .= $header . PHP_EOL;

        }

        return $headers;

    }


    /**
     * @return string prints application title
     */
    public function title()
    {
        return $this->title;
    }


    /**
     * @param string $layer prints layout from views/layers passed by name.
     * For example $layer = 'main/main-menu' will output layer from /views/layers/main/main-menu.php
     *
     * @param string $type supports php or html file extensions
     */
    public function layer($layer = '', $type = 'php')
    {

        $type = $type === 'php' ? 'php': 'html';
        $layer = trim($layer);
        if(empty($layer) || !file_exists(ROOT_DIR . '/views/layers/' . $layer . '.'.$type)){
            $layer = ROOT_DIR . '/views/layers/' . $this->layout . '.'.$type;
        } else{
            $layer = ROOT_DIR . '/views/layers/' . $layer . '.'.$type;
        }

        try{
            if(!file_exists($layer)){
                throw new \Exception("Basic layer \"{$layer}\" doesn't exist in directory");
            } else{
                include $layer;
            }
        } catch(\Exception $e){
            AppLog::noteError($e);
        }
    }


    /**
     * @return string rendered page content
     */
    public function content()
    {
        return $this->content;
    }

}