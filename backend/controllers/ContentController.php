<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 23.05.2015
 * Time: 20:07
 */

namespace app\controllers;


use app\core\App;
use app\core\base\Controller;

class ContentController extends Controller {




    public function pageIndex()
    {

        $this->redirectToController('content/show-contents');

    }

    public function pageShowContent()
    {

        $link = App::$request['url']['link'];

        $url = App::$protocol . '://' . SITE_ADDRESS . '/API/content.php?getContent&link='.$link;

        $content = file_get_contents($url);
        $content = json_decode($content, TRUE);


        $this->renderPage('content', ['content'=>$content]);
    }



    public function pageShowContents()
    {
        $category = !empty(App::$request['url']['category']) ? App::$request['url']['category'] : 'beauty';

        $page = !empty(App::$request['get']['page']) ? preg_replace('/[^0-9]/', '', App::$request['get']['page']) : 1;

        $perPage = 12;

        $url = App::$protocol . '://' . SITE_ADDRESS . '/API/content.php?getContents&category='.$category . '&page=' . $page . '&limit=' . $perPage;

        $contents = file_get_contents($url);

        $contents = json_decode($contents, true);

        if($contents){
            $total = $contents['total'];
            unset($contents['total']);
        } else{
            $total = 0;
        }

        $this->renderPage('contents', [
            'contents'=>$contents,
            'totalElements' => $total,
            'elementsOnPage' => $perPage,
        ]);

    }




}