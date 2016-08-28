<?php

namespace qui\base;

use qui\web\Html;

/**
 * Class View
 * @package qui\base
 */
class View
{
    /**
     * @var array
     */
    private $headers = [];

    /**
     * Default layout called as $this->layout();
     *
     * @var string
     */
    protected $layout = 'main/main-menu';

    /**
     * @var string
     */
    protected $page = 'index';

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var string
     */
    public $title = ''; //site title


    /**
     * @param string $metaName
     * @param string $metaData
     */
    public function addMeta($metaName, $metaData = '')
    {
        $this->addHeader('<meta ' . $metaName . '="' . Html::encode($metaData) . '"/>');
    }


    /**
     * @param string $header
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;
    }


    /**
     * @param string $path destination to including into headers js file
     * $this->registerJs('/js/rating/rating.js');
     *
     */
    public function bindJs($path = '')
    {
        $this->addHeader('<script type="text/javascript" src="' . Html::encode($path) . '"></script>');
    }


    /**
     * @param string $path destination to including into headers css file
     * $this->registerCss('/css/bootstrap/bootstrap.min.css');
     */
    public function bindCss($path = '')
    {
        $this->addHeader('<link rel="stylesheet" type="text/css" href="' . Html::encode($path) . '">');
    }


    /**
     * @param string $title setter pattern
     */
    public function setTitle($title = '')
    {
        $this->title = Html::encode($title);
    }


    /**
     * @param string $content
     */
    public function setContent($content = '')
    {
        $this->content = $content;
    }


    /**
     * @param string $page
     * @param array $variables extracting variables that will be locally visible in rendering page-view
     * @return string rendered file
     * @throws \InvalidArgumentException
     */
    public function render($page = '', array $variables = [])
    {
        $page .= '.php';

        if(!file_exists($page)){
            throw new \InvalidArgumentException('File ' . $page . ' does not exist');
        }
        
        ob_start();

        if (count($variables)) {
            extract($variables, EXTR_OVERWRITE);
        }

        include $page;

        return ob_get_clean();
    }


    /**
     * @param string|null $content raw text that will be sent to output as json
     * @param string $type type of rendering api answer. For now only json supported
     * if raw text is null,
     */
    public function raw($content = null, $type = 'json')
    {
        switch ($type) {
            case 'json':
                header('Content-Type: application/json; charset=utf-8');
                break;
            case 'html':
                header('Content-Type: text/html; charset=utf-8');
                break;
            case 'xml':
                header('Content-Type: text/html; charset=utf-8');
                break;
            default:
                header('Content-Type: text/html; charset=utf-8');
                break;
        }

        echo $content;
    }


    /**
     * @return string prints headers of application
     */
    public function headers()
    {
        return implode(PHP_EOL, $this->headers);
    }


    /**
     * @return string prints application title
     */
    public function title()
    {
        return $this->title;
    }


    /**
     * @return string rendered page content
     */
    public function content()
    {
        return $this->content;
    }
}