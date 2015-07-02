<?php
if(!defined('recombo')){die();}

$dbconnect = Database::getInstance();
$HTML='';

function catpath($catid){
   switch($catid)
   {
   case 1 : $path='krasota'; break;
   case 2 : $path='recommend'; break;
   case 5 : $path='kulinar'; break;
   default : $path=''; break;
   }
return $path;
}

//Базовые конфиги
$PAGE->site['metadata'][] = '<script type="text/javascript" src="/js/rating.js"></script>';



if(!isset($site['css'])){$site['css']  =  'main';}

//Выводим последние статьи из всех категорий !!!

for($i=1; $i<=5; $i++){
	
	$sql=$dbconnect->query("SELECT * FROM articles WHERE cat='".catpath($i)."' ORDER by pubdate DESC LIMIT  6");
	
	if(mysqli_num_rows($sql)>0){
		while($article=mysqli_fetch_assoc($sql)){
			$rating['ids'][] = $article['id'];
			$rating['rating'][] = $article['rating'];
			$rating['voters'][] = $article['rated'];
			$HTML.='<div class="article_preview"><a href="/content/'.$article['cat'].'/'.$article['article_link'].'.html">
						<img src="/images/articles/preview/'.$article['image'].'.jpg" alt="'.htmlspecialchars($article['headline']).'">
						</a><br/>
						<div class="article_link">
							<a  href="/content/'.$article['cat'].'/'.$article['article_link'].'.html">'.$article['headline'].'</a>
						</div>
						<div class="rating" id="article'.$article['id'].'">
									
						</div>
						<div class="article_date">'.$PAGE::normalDate($article['pubdate'],0).'</div>
				   </div><!--article_preview-->';
		}

		$HTML .= '<div style="width:100%; height:1%; clear:both;"></div><br>';
   }
}

if($rating){
			//Вывод рейтинга
	$HTML .= '<script>
			var RATING = new Rating();
			RATING.showMassRating(['.implode(',' , $rating['ids']).'], ['.implode(',' , $rating['rating']).'], ['.implode(',' , $rating['voters']).'], "article");
		  </script>';
}

require_once PATH.'/template/header.php';
echo $HTML;
require_once PATH.'/template/footer.php'; 

?>
