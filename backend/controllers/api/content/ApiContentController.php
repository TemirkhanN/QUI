<?php


namespace app\controllers\api\content;


use app\core\App;
use app\core\base\Controller;
use app\core\file_worker\Cache;
use app\models\database\Content;

class ApiContentController extends Controller {




    public function pageGetContent()
    {
        $cache = '';

        if (isset($_GET['link'])) {

            if (!preg_match('#([a-zA-Z0-9_-]{4,})#i', $_GET['link'])) {
                exit();
            }

            $link = $_GET['link'];

            $cache = Cache::getKey($link, 'content');

            if($cache === false) {
                $content = (new Content())->getRow(['link' => $link]);
                $json = json_encode($content, JSON_UNESCAPED_UNICODE);
                Cache::setKey($link, $json, 'content');
                $cache = $json;
            }
        }

        $this->renderApi($cache);
    }





    public function pageGetContents()
    {
        $cache = '';

        if (isset($_GET['category'])) {

            if (!preg_match('#([a-zA-Z0-9_-]{4,})#i', $_GET['category'])) {
                exit();
            }



            $limit = !empty($_GET['limit']) && intval($_GET['limit'])<=20 ? intval($_GET['limit']) : 10;
            $page = !empty($_GET['page']) && intval($_GET['page'])>=1 ? intval($_GET['page']) : 1;
            $offset = $limit*($page-1);

            $category = $_GET['category'];

            $cacheName = $category . '_page' . $page . '_offset' . $offset . '_limit' . $limit;
            $cache = Cache::getKey($cacheName, 'content');

            if($cache === false) {
                $contents = App::$db->getRecords(['*'], 'content', ['cat' => $category], [],$offset, $limit);
                $contentsCount = App::$db->countRecords('content', ['cat' => $category]);
                if($contents === false){
                    $contents = '';
                } else{
                    $contents['total'] = $contentsCount;
                }


                $json = json_encode($contents, JSON_UNESCAPED_UNICODE);
                Cache::setKey($cacheName, $json, 'content');
                $cache = $json;
            }
        }
        $this->renderApi($cache);
    }
}