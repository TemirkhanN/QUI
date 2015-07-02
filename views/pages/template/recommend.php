<?php 
if(!defined('recombo')){die();}


	$perpage = 12; //Статей на страницу
	
	$HTML = '<div style="clear:left;"></div>';
	
	
	//Параметры запроса статей
	$get_query = 'cat='.$PAGE->params['component'].'&perpage='.$perpage.'&page='.$PAGE->params['page'];
	
	$PAGE->site['css'] = 'recommend';
	$PAGE->site['title']  =  'Рекоммендации и отзывы';
	$PAGE->metaDesc('Мой мини-блог с обзором на косметику и другие предметы женской красоты');
	$PAGE->metaKeys('Рекоммендации, отзывы, обзоры, косметика');
	$PAGE->site['label'] = '<div id="label"></div>';
	
	//Если указана подкатегория
	if(isset($PAGE->params['subcat'])){
		$get_query .= '&subcat='.$PAGE->params['subcat'];
	}
	
	$articles = file_get_contents('http://vforme.su/API/articles/articles.php?'.$get_query);
	$articles = json_decode($articles, TRUE);

	//Если статьи по параметрам запроса имеются
	if( $articles != null ){


		
		$PAGE->site['metadata'][] = '<script type="text/javascript" src="/js/rating.js"></script>';
		
		

		//Выводим последние статьи из раздела
		foreach($articles as $article){
			
			$rating['ids'][] = $article['id'];
			$rating['rating'][] = $article['rating'];
			$rating['voters'][] = $article['rated'];
			
			$HTML.='<div class="article_preview"><a href="/content/'.$PAGE->params['component'].'/'.$article['article_link'].'.html">
						<img src="/images/articles/preview/'.$article['image'].'.jpg" alt="'.htmlspecialchars($article['headline']).'">
						</a>
						<div class="article_link">
							<a  href="/content/'.$PAGE->params['component'].'/'.$article['article_link'].'.html">'.$article['headline'].'</a>
						</div>
						<div class="article_preview_text">'.$article['preview'].'</div>
						<div style="clear:both; margin-left:40px;">
							<div class="article_date" style="float:left;">'.$PAGE::normalDate($article['pubdate'],0).'</div>
							<div class="rating" id="article'.$article['id'].'">
						</div>
						</div>
					</div><!--article_preview-->';
						
			
		}
			
			//Вывод рейтинга
			$HTML .= '<script>
							var RATING = new Rating();
							RATING.showMassRating(['.implode(',' , $rating['ids']).'], ['.implode(',' , $rating['rating']).'], ['.implode(',' , $rating['voters']).'], "article");
						</script>';
						
			//Постраничная Навигация
			$HTML .= $PAGE->pagination($perpage, $articles[0]['count']['total']);
	}








	require_once PATH."/template/header.php"; 
	echo $HTML;
	require_once PATH."/template/footer.php";

?>
