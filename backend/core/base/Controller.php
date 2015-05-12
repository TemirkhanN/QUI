<?php
/**
 * Created by PhpStorm.
 * User: temirkhan
 * Date: 17.04.15
 * Time: 22:15
 */

namespace app\core\base;


use app\core\Application;
use app\core\web\Html;

class Controller {

    private $allowedViews;
    protected $content;
    private $headers = [];
    protected $page;
    public $title;
    private $templateAbsPath;
    public $templateRelPath;
    protected $view;




    public function __construct($templateDir)
    {
        $this->allowedViews = ['php', 'html', 'xhtml', 'xml'];
        $this->templateAbsPath = $templateDir;
    }


    public function setPage($page)
    {


        while($pos = strpos($page, '-')){

            $page = mb_substr($page, 0, $pos).ucfirst(substr($page, $pos+1));

        }

        $this->page = $page;

    }



    public function setTheme($theme)
    {

        $theme =  $theme && !empty($theme) ? $theme : 'default';

        $this->templateAbsPath .= '/' . $theme;

    }



    public function setTitle($title)
    {
        $this->title = Html::encode($title);
    }



    public function title()
    {
        return $this->title;
    }



    public function getPage()
    {

        return $this->page;

    }



    public function run()
    {

        $action = 'page'.ucfirst($this->page);

        $this->templateRelPath = str_ireplace($_SERVER['DOCUMENT_ROOT'], '', $this->templateAbsPath);

        if(method_exists($this, $action)){
            $this->$action();
        }


    }






    protected function renderPage($view, $variables = [], $type = 'php')
    {

        try {
            if (in_array($type, $this->allowedViews)) {

                $className = (new \ReflectionClass($this))->getShortName();


                $this->view = $_SERVER['DOCUMENT_ROOT'] . '/../pages/' . lcfirst($className) . '/' . $view . '.' . $type;

                if(file_exists($this->view)){

                    $this->preRender($this->view, $variables);

                    $this->render();


                } else{
                    throw new \Exception('Viewer  "' . $view . '" doesn\'t exist in directory');
                }


            } else {
                throw new \Exception('Deprecated file type');
            }
        } catch(\Exception $error){
            Application::noteError($error);
        }


    }





    private function preRender($path = '', $variables = [])
    {
        ob_start();

        if(!empty($variables)){
            extract($variables);
        }

        include $path;

        return $this->content = ob_get_clean();
    }






    private function render()
    {

        $this->registerHeaders(Application::$templateHeaders);

        include $this->templateAbsPath . '/template.php';

    }





    public function headers()
    {
        $headers = '';

        foreach($this->headers as $header){

            $headers .= $header . PHP_EOL;

        }

        return $headers;

    }




    public function setHeadMeta($metaData = '')
    {
        $this->headers[] = $metaData;

    }



    private function registerHeaders($headers = [])
    {
        if(is_array($headers)){

            foreach($headers as $type=>$header){

                foreach($header as $headerFile) {
                    switch ($type) {
                        case 'js':
                            $this->registerJs($headerFile['path'], $headerFile['fileName']);
                            break;

                        case 'css':
                            $this->registerCss($headerFile['path'], $headerFile['fileName']);
                            break;

                        default:
                            break;
                    }
                }
            }

        }

    }

    public function registerJs($path = '', $fileName = null)
    {
        if($fileName){
            $path = Application::copyToTemplateFolder($path, 'js', $fileName);
        }


        $this->setHeadMeta('<script type="text/javascript" src="' . Html::encode($path) . '"></script>');
    }


    public function registerCss($path = '', $fileName = null)
    {
        if($fileName){
            $path = Application::copyToTemplateFolder($path, 'css', $fileName);
        }


        $this->setHeadMeta('<link rel="stylesheet" type="text/css" href="' . Html::encode($path) . '">');
    }




    public function content()
    {
        return $this->content;
    }


} 