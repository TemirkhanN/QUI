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
                $ulContent .= '<a '.self::generateAttributes($link).'>'.$link['title'].'</a>';
                $ulContent .= '</li>';
            } else{
                $ulContent .= '<li class="dropdown'.self::currentUrlIsActiveLink($link['href']).'">';
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


    private static function generateAttributes($attributes = [])
    {
        $attributes = array_intersect_key($attributes, array_flip(self::$allowedAttributes));
        $att = '';

        foreach($attributes as $key=>$value){
            $att .= ' '.$key.'="'.$value.'""';
        }

        return $att;

    }

}
