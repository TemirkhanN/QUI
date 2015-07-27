<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 06.06.2015
 * Time: 1:21
 */

namespace app\plugins\bootstrap;


class Bootstrap
{

    private static $allowedAttributes = ['href', 'title', 'class', 'id'];




    public static function navBar($links = [], $class = 'navbar-default', $brand = null)
    {
        $navBar = '<nav class="navbar '.$class.'">';
        $navBar .= '<div class="container">';
        $navBar .= '    <div class="navbar-header">';
        $navBar .= '        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>';
        if($brand!==null){
            $navBar .= '    <a class="navbar-brand" href="/">'.$brand.'</a>';
        }

        $navBar .= '    </div>
                        <div class="navbar-collapse collapse">';

        $navBar .= self::generateNavBar($links);

        $navBar .= '</div><!--/.nav-collapse -->
                </div>
        </nav>';

        return $navBar;

    }


    public static function generateNavBar($links, $class = null, $dropDown = false)
    {
        if($dropDown){
            $ulContent = $class == null ? '<ul class="dropdown-menu">': '<ul class="dropdown-menu '.$class.'">';
        } else{
            $ulContent = $class == null ? '<ul class="nav navbar-nav">': '<ul class="nav navbar-nav'.$class.'">';
        }


        foreach($links as $link){

            if(empty($link)){
                continue;
            }

            if(isset($link['decorative'])){
                $link['title'] = isset($link['title']) ? $link['title'] : '';
                $ulContent .= '<li class="'.$link['class'].'">'.$link['title'].'</li>';
                continue;
            }

            if(empty($link['href'])){
                $link['href'] = '#';
            }

            if(!isset($link['child'])) {
                if(!isset($link['class'])){
                    $ulContent .= self::currentUrlIsActiveLink($link['href'])!='' ? '<li class="active">' : '<li>';
                } else{
                    $ulContent .= self::currentUrlIsActiveLink($link['href'])!='' ? '<li class="active '.$link['class'].'">' : '<li class="'.$link['class'].'">';
                }
                $ulContent .= '<a '. self::generateAttributes($link).'>'.$link['title'].'</a>';
                $ulContent .= '</li>';
            } else{
                $ulContent .= '<li class="dropdown'. self::currentUrlIsActiveLink($link['href']).'">';
                $ulContent .= '<a href="'.$link['href'].'" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">'.$link['title'].'<span class="caret"></span></a>';
                $ulContent .= self::generateNavBar($link['child'], null, true);
            }
        }
        $ulContent .= '</ul>';

        return $ulContent;

    }



    private static function currentUrlIsActiveLink($linkUrl = '')
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if($linkUrl === $path){
            return ' active';
        }
        return '';
    }


    public static function isActiveLink($url = '')
    {
        return self::currentUrlIsActiveLink($url)!='' ? ' class="active"' : '';
    }


    private static function generateAttributes($attributes = [])
    {
        $attributes = array_intersect_key($attributes, array_flip(self::$allowedAttributes));
        $att = '';

        foreach($attributes as $key=>$value){
            $att .= ' '.$key.'="'.trim(strip_tags($value)).'"';
        }

        return $att;

    }





    public static function carouselSlider($items = [], $attributes = [])
    {
        $attributes['class'] = !empty($attributes['class']) ? 'carousel slide ' . $attributes['class'] : 'carousel slide';
        $attributes['id'] = !empty($attributes['id']) ? $attributes['id']  : 'carousel-id-not-set';
        $attributes['interval'] = !empty($attributes['interval']) ? $attributes['interval']*1000  : false;


        $carousel = '
            <div '. self::generateAttributes($attributes).' class="carousel '.$attributes['class'].' carousel-fit" data-ride="carousel" data-interval="'.$attributes['interval'].'">
                <ol class="carousel-indicators">
                    '. self::generateCarouselSwitchers($items, $attributes['id']).'
                </ol>
                <div class="carousel-inner" role="listbox">
                    '. self::generateCarouselSlides($items).'
                </div>
                <a class="left carousel-control" href="#' . $attributes['id'] . '" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#' . $attributes['id'] . '" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>';

        return $carousel;

    }


    private static function generateCarouselSwitchers($items = [], $carouselId = '')
    {
        $switchers = '';
        $total = count($items);

        if($total>0){
            for($i=0; $i<$total; $i++){

                $active = $i === 0 ? 'class="active"' : '';
                $switchers.= '<li data-target="#' . $carouselId . '" data-slide-to="'.$i.'" '.$active.'></li>';
            }

        }

        return $switchers;

    }


    private static function generateCarouselSlides($items = [])
    {
        $iteratorCount = 0;
        $slides = '';
        foreach($items as $item){

            $item['class'] = !empty($item['class']) ? $item['class'] .= ' item' : 'item';
            $item['href'] = !empty($item['href']) ? $item['href'] : '#';
            $item['name'] = !empty($item['name']) ? $item['name'] : 'Slide '.($iteratorCount+1);

            if($iteratorCount===0){
                $item['class'] .= ' active';
            }

            $slides .= '
                    <div'. self::generateAttributes($item).'>
                        <a href="'.$item['href'].'">
                            <img src="'.$item['image'].'" title="'.$item['name'].'" alt="'.$item['name'].'">
                        </a>
                    </div>';



            ++$iteratorCount;
        }

        return $slides;

    }





    public static function table($schema = [], $items = [], $attributes = [])
    {
        $attributes['class'] = !empty($attributes['class']) ? 'table ' . $attributes['class'] : 'table';
        $table = '<table '. self::generateAttributes($attributes).'>';
        $table .= '<thead>'. self::generateTableRow($schema, 'th').'</thead>';

        $table .= '<tbody>';
        foreach($items as $item){

            $table .= self::generateTableRow($item, 'td');
        }
        $table .= '</tbody>';

        $table .= '</table>';

        return $table;

    }

    private static function generateTableRow($rowData = [], $type = 'td')
    {
        $tr = '<tr>';
        foreach($rowData as $data){
            $tr .= '<'.$type.'>'.$data.'</'.$type.'>';
        }
        $tr .= '</tr>';

        return $tr;
    }






    public static function breadcrumbs($breadcrumbs = [])
    {

        $html = '<ol class="breadcrumb">';

        if(!empty($breadcrumbs) && is_array($breadcrumbs)){
            $totalChains = count($breadcrumbs);
            $currentChain = 0;

            foreach($breadcrumbs as $breadcrumb){
                ++$currentChain;
                $html .= $currentChain===$totalChains ? '<li class="active">' : '<li>';
                $html .= !empty($breadcrumb['icon']) ? $breadcrumb['icon'].' ' : '';
                $html .= !empty($breadcrumb['href']) && $currentChain!==$totalChains ? '<a href="'.$breadcrumb['href'].'">' : '';
                $html .= !empty($breadcrumb['title']) ? $breadcrumb['title'] : 'пустая цепочка';
                $html .= $currentChain!==$totalChains ? '</a>' : '';
                $html .= '</li>';

            }
        }
        $html .= '</ol>';


        return $html;

    }

}
