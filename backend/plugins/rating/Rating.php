<?php 

namespace app\plugins\rating;



use app\core\App;
use app\core\base\Plugin;
use app\core\web\UrlManager;

class Rating implements  Plugin
{

    private static $availableTargets = ['content', 'photo']; //List of targets available for vote
    private static $prefix = 'rating_plugin_'; // This is for post requests filter
    private static $targets = [];
    private static $ratingTableName = 'plugin_rating';



    public static function init()
    {
        $routeToApi = ['route'=>'^/API/rating_plugin.php\?(getRating|setRating)+$', 'full' => true, 'action'=>'rating/{0}'];
        UrlManager::addRule($routeToApi);
        $postKey = self::$prefix.'targets';
        if(!empty($_POST[$postKey]) && is_array($_POST[$postKey])){
            self::createRatingTable();
            self::$targets = $_POST[$postKey];
        }
    }



    private static function createRatingTable()
    {

        if(App::$db->checkTableExist(self::$ratingTableName) == false) {
            App::$db->query("CREATE TABLE IF NOT EXISTS `" . self::$ratingTableName . "` (
                                  `id` INT(11) AUTO_INCREMENT,
                                  `target` VARCHAR (50) NOT NULL DEFAULT '',
                                  `target_id` INT(11),
                                  `user_hash` VARCHAR(32) NOT NULL DEFAULT '',
                                  `rate` TINYINT(1) DEFAULT 0,
                                  PRIMARY KEY (`id`)
                              ) ENGINE=InnoDB");

        }
    }



    public static function getClassVariable($var)
    {
        return isset(self::${$var}) ? self::${$var} : null;
    }






    private static function checkParamsPassed()
    {
        $prefix = Rating::getClassVariable('prefix');

        if(!empty($_POST[$prefix.'target']) && !empty($_POST[$prefix.'target_id']) && !empty($_POST[$prefix.'rating'])){
            return true;
        }

        return false;
    }




    public static function checkVotePossible()
    {
        if(self::checkParamsPassed()){

            $prefix = Rating::getClassVariable('prefix');
            $target = $_POST[$prefix.'target'];
            $targetId = intval($_POST[$prefix.'target_id']);
            $rating = intval($_POST[$prefix.'rating']);
            $userHash = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);

            if($rating>0 && $targetId>0 && in_array($target, self::$availableTargets)){

                //if user has not voted yet return true
                if(false == App::$db->countRecords(Rating::getClassVariable('ratingTableName'), ['user_hash'=>$userHash, 'target'=>$target, 'target_id'=>$targetId])){
                    return true;
                }
            }
        }

        return false;
    }


    public static function getTargetsRating($targets = [])
    {

        if(empty($targets)){
            return false;
        }


        $query = 'SELECT `user_hash`, `rate`, `target`, `target_id` FROM `'.Rating::getClassVariable('ratingTableName').'`';
        $where = '';
        $whereParams = [];


        foreach($targets as $key=>$items){
            $targets[$key]= $items = array_keys($items);

            $where .= empty($where) ? ' WHERE ' : ' OR ';
            $where .= '(`target`=:'.$key;
            $whereParams[$key] = $key;
            $ids = '`target_id` IN(';

            foreach($items as $itemId){

                $ids .= ':id'.$itemId.',';
                $whereParams['id'.$itemId] = $itemId;
            }
            $ids = mb_strcut($ids, 0,-1).')'; //To delete last comma and complete IN()

            $where .= ' AND '.$ids.')';
        }

        $query .= $where;


        $rates = App::$db->executeQuery($query, $whereParams)->fetchAll();


        $targets = self::filRequiredInformation($targets, $rates);

        return $targets;
    }



    private static function filRequiredInformation($targets, $rateItems)
    {

        $result = [];
        $userHash = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);

        foreach($targets as $target=>$items){

            $result[$target] = [];

            foreach($items as $itemId){

                foreach($rateItems as $key=>$rateItem){

                    if($rateItem['target'] == $target && $rateItem['target_id'] == $itemId){
                        $result[$target][$itemId]['totalVoters'] = !isset($result[$target][$itemId]['totalVoters']) ? 1 : ($result[$target][$itemId]['totalVoters']+1);
                        $result[$target][$itemId]['userVoted'] = $userHash === $rateItem['user_hash'];

                       $result[$target][$itemId]['rating'] = !isset($result[$target][$itemId]['rating']) ? $rateItem['rate'] : ($result[$target][$itemId]['rating']+$rateItem['rate']);
                        unset($rateItems[$key]);
                    }
                }
            }


        }

        return $result;
    }

}



