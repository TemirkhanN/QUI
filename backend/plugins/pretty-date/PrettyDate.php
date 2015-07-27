<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 23.05.2015
 * Time: 20:13
 */

namespace app\plugins\pretty_date;



class PrettyDate
{

    private static $localization = [
        "January" => "Января","February" => "Февраля",
        "March" => "Марта","April" => "Апреля","May" => "Мая",
        "June" => "Июня","July" => "Июля","August" => "Августа",
        "September" => "Сентября","October" => "Октября",
        "November" => "Ноября","December" => "Декабря"
    ];





    /**This is also not necessary for non-russians
     *
     * @param string $currentDate текущая дата в формате date, datetime  и т.д
     * @param bool $withHours
     * @return string  Возвращает локализованную отформатированную дату. Например 15:29 / 25 Сентября 2015
     */
    public static function convert($currentDate = '', $withHours = false)
    {

        $today = date("d-m-Y");

        $yesterday = date("d-m-Y", time()-60*60*24);

        $currentTime = strtotime($currentDate);

        switch($currentDate){
            case $today:
                return 'Сегодня в '.date("G:i", $currentTime);
            break;

            case $yesterday:
                return 'Вчера в '.date("G:i", $currentTime);
            break;

            default:
                if($withHours){
                    return  strtr(date('G:i / j F Y', $currentTime), self::$localization);
                } else{
                    return  strtr(date('j F Y', $currentTime), self::$localization);
                }
            break;
        }

    }

}