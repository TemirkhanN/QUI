<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 23.05.2015
 * Time: 20:21
 */

namespace app\plugins\mobile\detector;


class MobileDetector {

    //Перенаправляет на мобильную или полную версию сайта
    public static function siteVersion(){

        if( isset($_SESSION['siteversion']) ){

            if( $_SESSION['siteversion']==='portable' ){

                header("Location: http://m.{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");

            } elseif( $_SESSION['siteversion']==='full' ){

                header("Location: http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");

            }
        }

        if( isset($_SERVER['HTTP_USER_AGENT']) ){

            $browser = $_SERVER['HTTP_USER_AGENT'];

        }

        $mobiles = array(	'Mobile'   ,  'Symbian'    ,   'Opera M',
            'Android'   ,   'HTC_'   ,   'Fennec/',
            'Blackberry'   ,  'Windows Phone',
            'WP7'   ,   'WP8',
        );

        foreach( $mobiles as $mobile ){

            if( strpos($browser, $mobile) !== false ){
                header('Location: http://m.'.self::secureData($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
                break;
            }
        }

    }
###function end###

}