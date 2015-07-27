<?php

namespace app\core\base;


use app\core\App;
use app\core\file_worker\File;
use app\core\web\Html;

/**
 * Class Controller
 * @package app\core\base
 *
 *
 * Mostly classes logic protected and not available for child classes that you will create at /backend/controllers/ or in plugins
 */
class Controller
{

    private $allowedViews = ['php', 'html', 'xhtml', 'xml'];
    private $headers = [];
    private $registeredHeaders = ['css'=>[], 'js'=>[]]; // To avoid duplicate css|js headers
    protected $layout = 'main/main-menu'; //default layout called as $this->layout();
    protected $page = 'index';
    protected $content = '';
    protected $customTemplate = false; //if set to true template.php won't be used (only rendering view)
    public $title = ''; //site title
    private $templateAbsPath; //absolute path to template folder(for backend usage)
    public $templateRelPath; //relative path to template folder(for frontend usage)



    public function __construct($templateDir)
    {
        $this->templateAbsPath = $templateDir;
    }


    /**
     * @param string $page page name from view folder that will be rendered
     */
    public function setPage($page = 'index')
    {
        $page =  App::toCamelCase($page);
        $this->page = 'page'.ucfirst($page);
    }


    /**
     * @param string $theme directory in template folder where template.php lay.
     * For example $theme = 'modern' will get template.php from /templates/modern/template.php while rendering page
     */
    public function setTheme($theme = '')
    {
        $theme =  $theme && !empty($theme) ? $theme : 'default';
        $this->templateAbsPath .= '/' . $theme;
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


    /**
     * @param string $theme default
     * @param string $page index
     * @param string $title site title
     */
    public function run($theme, $page, $title)
    {
        $this->setTheme($theme);

        $this->setPage($page);

        $this->setTitle($title);

        $this->templateRelPath = str_ireplace(FRONTEND_DIR, '', $this->templateAbsPath);


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


    /**
     * @param string $page rendering view-page name
     * @param array $variables extracting variables that will be locally visible in rendering page-view
     * @param string $type extension of "rendering" file
     */
    protected function renderPage($page, $variables = [], $type = 'php')
    {

        try {
            if (in_array($type, $this->allowedViews)) {

                /* prevents replacing controller's non-system word. just to be sure it won't happen
                 * !strongly recommended not to use such controller names(i.e. adminControllerController.
                 */
                $className = preg_replace('#Controller$#', '', (new \ReflectionClass($this))->getShortName());

                /* replaces camelCase by camel-case */
                $className = strtolower(preg_replace('#([A-Z]{1})#', '-${1}', lcfirst($className)));


                $pageFile = ROOT_DIR . '/views/pages/' . $className . '/' . $page . '.' . $type;

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


    /**
     * @param string $page
     * @param array $variables extracting variables that will be locally visible in rendering page-view
     */
    private function renderContent($page = '', $variables = [])
    {
        ob_start();

        if(!empty($variables)){
            extract($variables);
        }

        include $page;

        $this->content = ob_get_clean();
    }


    /**
     * Renders template.php after content in it will be rendered and cleaned from buffer
     */
    private function renderTemplate()
    {
        $this->registerFiles(App::$templateHeaders);
        include $this->templateAbsPath . '/template.php';
    }


    /**
     * @param null $rawText raw text that will be sent to output as json
     * @param string $rawType type of rendering api answer. For now only json supported
     * if raw text is null,
     */
    public function renderApi($rawText = null, $rawType = 'json')
    {
        App::switchOffDebug();


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
     * @param array $files in-common tis css and js that shall be printed in headers
     * Also it may be public files that shall be in template dir. For example: image files or fonts
     */
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


    /**
     * @param string $path destination to including into headers js file that begins from template dir.
     * $this->registerJs('js/rating/rating.js');
     *
     * @param null $file ['fileName', 'folder'] if file not just registering but shall be included or moved to public
     * from somewhere outside document_root
     */
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



    /**
     * @param string $path destination to including into headers css file that begins from template dir.
     * $this->registerCss('css/bootstrap/bootstrap.min.css');
     *
     * @param null $file ['fileName', 'folder'] if file not just registering but shall be included or moved to public
     * from somewhere outside document_root
     */
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


    /**
     * @param string $path path to copying file
     * @param array $file extra file information such as extension, file name and sub-folder name where it shall be copied
     */
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
     * @param string $layoutName prints layout from views/layouts passed by name.
     * For example $layoutName = 'main/main-menu' will output layout from /views/layouts/main/main-menu.php
     *
     * @param string $type supports php or html file extensions
     */
    public function layout($layoutName = '', $type = 'php')
    {

        $type = $type === 'php' ? 'php': 'html';
        $layoutName = trim($layoutName);
        if(empty($layoutName) || !file_exists(ROOT_DIR . '/views/layouts/' . $layoutName . '.'.$type)){
            $layoutName = ROOT_DIR . '/views/layouts/' . $this->layout . '.'.$type;
        } else{
            $layoutName = ROOT_DIR . '/views/layouts/' . $layoutName . '.'.$type;
        }

        try{
            if(!file_exists($layoutName)){
                throw new \Exception("Base layout \"{$layoutName}\" doesn't exist in directory");
            } else{
                include $layoutName;
            }
        } catch(\Exception $e){
            App::noteError($e);
        }
    }


    /**
     * @return string prints applications content body
     */
    public function content()
    {
        return $this->content;
    }


    /**
     * @param string $url where redirection shall happen
     * it may be absolute url or relative path to local resource
     * For example^ $this->redirect('/login/'); $this->redirect('https://github.com');
     *
     * @param int $code header answer code while redirecting
     */
    public function redirectToUrl($url = '', $code = 303)
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
    public function redirect($request = '')
    {
        App::$app->runController($request);
    }


    /**
     * @param bool $status true if template.php shall not be used while rendering page
     * that's required while rendering unique page.(nothing more than $this->content() will be printed)
     */
    public function setCustomTemplate($status = false)
    {
        $this->customTemplate = (bool) $status;
    }


    /**
     * @return bool getter pattern for customTemplate check
     */
    public function isCustomTemplate()
    {
        return $this->customTemplate;
    }


}