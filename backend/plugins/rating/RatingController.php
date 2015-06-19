<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 14.06.2015
 * Time: 10:09
 */

namespace app\controllers;


use app\core\App;
use app\core\base\Controller;
use app\plugins\rating\Rating;
use app\plugins\rating\RatingModel;

class RatingController extends Controller {

    public function pageGetRating()
    {
        $postKey = Rating::getClassVariable('prefix').'targets';

        if(!empty($_POST[$postKey]) && is_array($_POST[$postKey])){
            $rates = Rating::getTargetsRating($_POST[$postKey]);
            $this->renderApi(json_encode($rates, JSON_UNESCAPED_UNICODE));
        }

    }



    public function pageSetRating()
    {

        if(Rating::checkVotePossible()) {

            $userHash = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
            $prefix = Rating::getClassVariable('prefix');
            $target = $_POST[$prefix.'target'];
            $targetId = intval($_POST[$prefix.'target_id']);
            $rating = intval($_POST[$prefix.'rating']);

            $rated = App::$db->addRecord(['target' => $target, 'target_id' => $targetId, 'rate'=>$rating, 'user_hash' => $userHash], Rating::getClassVariable('ratingTableName'));

            if ($rated > 0) {
                echo $rated;
            }
        }

    }



}