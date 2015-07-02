<?php 

if (!defined('recombo')){exit();}

function detectParent($needle, $array){
	foreach ($array as $key=>$value){
		if (in_array($needle, $value)){
			$val = detectParent($key, $array);
			
			if ($val){
				return $val.'/'.$key;
			} else {
				return $key;
			}
		}
	}
	return false;
}


$PAGE->site['css'] = 'main';
$PAGE->site['title']  =  'Карта сайта';
$PAGE->metaDesc('Карта сайта');
$PAGE->metaKeys('Карта сайта, ссылки, vforme.su');



$DB = Database::getInstance();
$HTML = '<div class="sitemap"><center><h1>Карта сайта</h1></center><br>'."\n";
			


//Карта сайта с категориями контента
$query = $DB->query("SELECT * FROM sitemap ORDER BY parent");

if ($query AND $query->num_rows){
	
	while ($sitemap = $query->fetch_assoc()){

		$sitemaps[$sitemap['parent']][] = $sitemap['child'];
		$allparts[$sitemap['pathname']] = $sitemap['child'];
	}
	
	foreach ($allparts as $key=>$value){

		$parent = detectParent($value, $sitemaps);
		
		
		if($parent){
			$urlmap[$key] = $parent.'/'.$value;
		}

	}

	asort($urlmap);
	
	
	$parent = 'ooo';
	foreach($urlmap as $key=>$value){
		
		if(strpos($value, $parent)!==false){
			$HTML .= '&emsp;<a href="/'.$value.'">'.$key.'</a><br>'."\n";
		}else {
			$HTML .= '<a href="/'.$value.'">'.$key.'</a><br>'."\n";
			$parent = $value;
		}

		
	}
}



$HTML .= '</div>';



require_once PATH."/template/header.php";
echo $HTML;
require_once PATH."/template/footer.php";

?>
