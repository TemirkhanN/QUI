<?php 

namespace app\plugins\rating;




class RatingAjaxHandler {

}
$json = '{"rate" : 0, "voted" : false}';


//Если указано, к чему получается рейтинг и это допустимый элемент
if (!empty($_POST['target'])){
	
	
	if (isset($_POST['target_id']) AND isset($_POST['rating'])){ //
		
		$id = intval($_POST['target_id']);
		$rate = intval($_POST['rating']);
		
		if ( $id<=0 OR $rate<=0 OR $rate>5 ){exit();}
		$target = htmlspecialchars(strip_tags($_POST['target']));
		
		$DB = Database::getInstance(); //Подключаемся к базе
		$userhash = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']); //Получаем уникальный идентификатор пользователя
		
		//Создаем запрос к базе данных
		$query = $DB -> query("SELECT `rate` FROM `votes` WHERE `user_hash`='$userhash' AND `target_id` = $id AND `target`='$target' LIMIT 1");

		if ( $query && $query->num_rows==0){
			$setRating = $DB -> query("INSERT INTO `votes` SET `target`='$target', `target_id`='$id', `user_hash`='$userhash', `rate`='$rate' ");
				if ( $setRating ){
					$DB->query("UPDATE `$target` SET `rating`=`rating`+$rate, `rated`=`rated`+1 WHERE `id`=$id LIMIT 1");
				}
		
			$json = '{"rate" : '.$rate.', "voted": true}';
		}
	}
}



