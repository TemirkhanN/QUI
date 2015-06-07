<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 23.05.2015
 * Time: 20:13
 */

namespace app\plugins\pretty\date;


class PrettyDate {





//Возвращает локализованную отформатированную дату. Например 15:29 / 25 Сентября 2014
    public static function normalDate($datavalue, $withhours=0){

        $today = date("d-m-Y");

        $yesterday = date("d-m-Y", time()-86400);

        $datavalue = strtotime($datavalue);

        $datatemp = date('d-m-Y',$datavalue);


        if( $datatemp==$today ){

            return 'Сегодня в '.date("G:i", $datavalue);

        } elseif( $datatemp==$yesterday ){

            return 'Вчера в '.date("G:i", $datavalue);

        } else{
            $datetranslate = array(
                "January" => "Января","February" => "Февраля",
                "March" => "Марта","April" => "Апреля","May" => "Мая",
                "June" => "Июня","July" => "Июля","August" => "Августа",
                "September" => "Сентября","October" => "Октября",
                "November" => "Ноября","December" => "Декабря"
            );

            if( $withhours===1 ){

                return  strtr(date('G:i / j F Y', $datavalue), $datetranslate);

            } else{

                return  strtr(date('j F Y', $datavalue), $datetranslate);

            }

        }
    }
###function end###

}