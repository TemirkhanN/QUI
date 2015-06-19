<?php
/**
 * Created by PhpStorm.
 * User: temirkhan
 * Date: 17.04.15
 * Time: 22:15
 */

namespace app\core\base;


use app\core\App;
use app\core\file\worker\File;
use app\core\web\Html;

class Controller
{

    private $allowedViews = ['php', 'html', 'xhtml', 'xml'];
    private $headers = [];
    private $registeredHeaders = ['css'=>[], 'js'=>[]]; // To avoid duplicate css|js headers
    protected $layout = 'main/main-menu';
    protected $page = 'index';
    protected $content = '';
    protected $customTemplate = false;
    public $title = '';
    private $templateAbsPath;
    public $templateRelPath;



    public function __construct($templateDir)
    {
        $this->templateAbsPath = $templateDir;
    }


    public function setPage($page)
    {
        $page =  App::toCamelCase($page);
        $this->page = 'page'.ucfirst($page);
    }




    public function setTheme($theme)
    {
        $theme =  $theme && !empty($theme) ? $theme : 'default';
        $this->templateAbsPath .= '/' . $theme;
    }


    public function setMetaDesc($description = '')
    {
        $this->setHeadMeta('<meta desc=" ' .Html::encode($description). '">');
    }



    public function setMetaKeys($tags = '')
    {
        if(is_array($tags)){
            $tags = implode(',', $tags);
        }
        $this->setHeadMeta('<meta keywords=" ' .Html::encode($tags). '">');
    }

    public function setHeadMeta($metaData = '')
    {
        static $tab = '';


        $this->headers[] = $tab.$metaData;

        if($tab === ''){
            $tab = '    ';
        }
    }



    public function setTitle($title = '')
    {
        $this->title = Html::encode($title);
    }





    public function run($theme, $page, $title)
    {
        $this->setTheme($theme);

        $this->setPage($page);

        $this->setTitle($title);

        $this->templateRelPath = str_ireplace($_SERVER['DOCUMENT_ROOT'], '', $this->templateAbsPath);

        try {
            if (method_exists($this, $this->page)) {
                $this->{$this->page}();
            } else{
                throw new \Exception('Method '.$this->page.' does not exist in controller ' . (new \ReflectionClass($this))->getName());
            }
        } catch(\Exception $error){
            App::noteError($error);
        }

    }






    protected function renderPage($page, $variables = [], $type = 'php')
    {

        try {
            if (in_array($type, $this->allowedViews)) {

                $className = preg_replace('#Controller$#', '', (new \ReflectionClass($this))->getShortName());

                $className = strtolower(preg_replace('#([A-Z]{1})#', '-${1}', lcfirst($className)));


                $pageFile = $_SERVER['DOCUMENT_ROOT'] . '/../pages/' . $className . '/' . $page . '.' . $type;

                if(file_exists($pageFile)){

                    $this->renderContent($pageFile, $variables);
                    $this->renderTemplate();

                } else{
                    throw new \Exception('Page  "' . $page . '" doesn\'t exist in directory');
                }


            } else {
                throw new \Exception('Deprecated file type');
            }
        } catch(\Exception $error){
            App::noteError($error);
        }


    }





    private function renderContent($page = '', $variables = [])
    {
        ob_start();

        if(!empty($variables)){
            extract($variables);
        }

        include $page;

        $this->content = ob_get_clean();
    }





    private function renderTemplate()
    {
        $this->registerFiles(App::$templateHeaders);
        include $this->templateAbsPath . '/template.php';
    }




    public function renderApi($rawText = null)
    {
        App::switchOffDebug();

        header('Content-Type: application/json; charset=utf-8');
        if($rawText != null){
            echo $rawText;
        } else {
            echo $this->content();
        }
    }



    private function registerFiles($files = [])
    {
        if(is_array($files)){

            foreach($files as $type=>$file){

                foreach($file as $fileData) {

                    $checkDuplicate = isset($this->registeredHeaders[$type][$fileData['fileName']]);

                    $this->registeredHeaders[$type][$fileData['fileName']] = $fileData['path'];
                    if($checkDuplicate){
                        continue;
                    }



                    switch ($type) {
                        case 'js':
                            $this->registerJs($fileData['path'], $fileData);
                            break;

                        case 'css':
                            $this->registerCss($fileData['path'], $fileData);
                            break;



                        default:
                            $this->loadFileToTemplateDir($fileData['path'], $fileData);
                            break;
                    }
                }
            }

        }

    }



    public function registerJs($path = '', $file = null)
    {
        if($file){
            $destinationDir = $this->templateAbsPath.'/js/'.$file['folder'];
            $path = File::copyFileToFolder($path, $destinationDir.'/'.$file['fileName'].'.js');
        } else{
            $path = $this->templateRelPath .'/'.$path;
        }


        $this->setHeadMeta('<script type="text/javascript" src="' . Html::encode($path) . '"></script>');
    }


    public function registerCss($path = '', $file = null)
    {
        if($file){
            $destinationDir = $this->templateAbsPath.'/css/'.$file['folder'];
            $path = File::copyFileToFolder($path, $destinationDir.'/'.$file['fileName'].'.css');
        } else{
            $path = $this->templateRelPath .'/'.$path;
        }


        $this->setHeadMeta('<link rel="stylesheet" type="text/css" href="' . Html::encode($path) . '">');
    }



    private function loadFileToTemplateDir($path = '', $file = [])
    {

        if(!empty($file)){
            $destinationDir = $this->templateAbsPath;
            if(in_array(strtolower($file['type']), ['jpg','jpeg', 'gif', 'png','bmp'])){
                $destinationDir .= '/images';
            } else if(in_array(strtolower($file['type']), ['eot', 'ttf', 'woff', 'woff2', 'fnt', 'svg', 'otf'])){
                $destinationDir .= '/css';
            } else{
                $destinationDir .= '/'.$file['type'];
            }

            $destinationDir .= !empty($file['folder']) ? '/'.$file['folder'] : '';
            $fileName = $file['fileName'] . '.' . $file['type'];
            File::copyFileToFolder($path, $destinationDir.'/'.$fileName);



        }
    }



    public function headers()
    {
        $headers = '';

        foreach($this->headers as $header){

            $headers .= $header . PHP_EOL;

        }

        return $headers;

    }



    public function title()
    {
        return $this->title;
    }



    public function layout($layoutName = '')
    {
        $layoutName = trim($layoutName);
        if(empty($layoutName) || !file_exists($_SERVER['DOCUMENT_ROOT'] . '/../layouts/' . $layoutName . '.php')){
            $layoutName = $_SERVER['DOCUMENT_ROOT'] . '/../layouts/' . $this->layout . '.php';
        } else{
            $layoutName = $_SERVER['DOCUMENT_ROOT'] . '/../layouts/' . $layoutName . '.php';
        }
        include $layoutName;
    }





    public function content()
    {
        return $this->content;
    }




    public function redirect($url = '', $code = 303)
    {
        if(!preg_match("~^http(s?)://~", $url)) {
            header("Location:" . $url, true, $code);
        } elseif(filter_var($url, FILTER_VALIDATE_URL)){
            header("Location:/redirect.php?url=".$url, true, $code);
        }
        exit();
    }


    /**
     * @param string $request  redirects from current controller to another one
     * for example myController/action  or  myController
     */
    public function redirectToController($request = '')
    {
        App::$app->runController($request);
    }




    public function setCustomTemplate($status = false)
    {
        $this->customTemplate = (bool) $status;
    }



    public function isCustomTemplate()
    {
        return $this->customTemplate;
    }


} 