<?php 

if(!defined('recombo')){ die(); }


$PAGE->metaDesc('Страница не найдена');

$PAGE->metaKeys('404, not found');

$PAGE->site['title'] = '404';

header("HTTP/1.0 404 Not Found");

$HTML = '<style>
			#notFound{
				font: normal normal 140px Trebuchet MS;
				text-align:center;
			}
			#notFound p{
				font-size:32px;
			}
		</style>
		<div id="notFound">
			404<br/>
			<p>НЕ НАЙДЕНО</p>
		</div>';
	  

###function end###



require_once PATH."/template/header.php"; 

echo $HTML;

require_once PATH."/template/footer.php";
	
