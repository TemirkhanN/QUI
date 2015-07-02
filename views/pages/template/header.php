<?php if(!defined('recombo')){exit();} ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $PAGE->site['title']; ?></title>
		<meta name="Author"  content="Ксения Yami Владимировна">
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="description" content="<?php echo $PAGE->site['description']; ?>">
		<meta name="keywords" content="<?php echo $PAGE->site['keywords']; ?>">
		<link rel="stylesheet" type="text/css" href="/design/general.css">
		<link rel="stylesheet" type="text/css" href="/design/<?php echo $this->site['css']; ?>/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
		<script type="text/javascript">
		window.jQuery || document.write('<script type="text/javascript" src="/js/jquery.min.js"><\/script>');
		</script>
		
<?php if(isset($PAGE->site['metadata'])){echo implode("\r\n", $PAGE->site['metadata'])."\r\n";} ?>
</head>
<body>



		<div class="mainalign"><!--mainalign-->
		<div id="label"></div>
		
			<ul id="sdt_menu" class="sdt_menu" >
				<li>
					<a href="/">
						<img src="/design/images/1.jpg" alt=""/>
						<span class="sdt_active"></span>
						<span class="sdt_wrap">
							<span class="sdt_link">HOME</span>
							<span class="sdt_descr">Главная страница</span>
						</span>
					</a>
				</li>
				<li>
					<a href="/content/krasota" title="Для тех, кто следит за собой и ценит свое здоровье">
						<img src="/design/images/2.jpg" alt=""/>
						<span class="sdt_active"></span>
						<span class="sdt_wrap">
							<span class="sdt_link">Beauty</span>
							<span class="sdt_descr">Красота и здоровье</span>
						</span>
					</a>
					<div class="sdt_box">
							<a href="/content/krasota/pitanie_i_diety">Питание и диеты</a>
							<a href="/content/krasota/uhod_za_telom">Уход за телом</a>
							<a href="/content/krasota/uhod_za_licom">Уход за лицом</a>
							<a href="/content/krasota/uhod_za_volosami">Уход за волосами</a>
					</div>
				</li>
				<li>
					<a href="/content/recommend" title="Впечатления и рекомендации по той или иной косметике и товарам">
						<img src="/design/images/3.jpg" alt=""/>
						<span class="sdt_active"></span>
						<span class="sdt_wrap">
							<span class="sdt_link">Recommend</span>
							<span class="sdt_descr">Отзывы и впечатления</span>
						</span>
					</a>
					<div class="sdt_box">
					</div>
				</li>
				<li>
					<a href="/">
						<img src="/design/images/4.jpg" alt=""/>
						<span class="sdt_active"></span>
						<span class="sdt_wrap">
							<span class="sdt_link">Indevelop</span>
							<span class="sdt_descr">Раздел в разработке</span>
						</span>
					</a>
					<div class="sdt_box">
					</div>
				</li>
				<li>
					<a href="/">
						<img src="/design/images/5.jpg" alt=""/>
						<span class="sdt_active"></span>
						<span class="sdt_wrap">
							<span class="sdt_link">Indevelop</span>
							<span class="sdt_descr">Раздел в разработке</span>
						</span>
					</a>
					<div class="sdt_box">
					</div>
				</li>
				<li>
					<a href="/content/kulinar" title="Побалуйте себя и близких вкусностями">
						<img src="/design/images/6.jpg" alt=""/>
						<span class="sdt_active"></span>
						<span class="sdt_wrap">
							<span class="sdt_link">COOK</span>
							<span class="sdt_descr">Кулинария</span>
						</span>
					</a>
					<div class="sdt_box">
					<table>
					 <tr>
					  <td style="width:240px;">
						<a href="/content/kulinar/nizkokalorijnye_bljuda">Низкокалорийные блюда</a>
						<a href="/content/kulinar/vegetarianskie_bljuda">Вегетарианские блюда</a>
						<a href="/content/kulinar/kuhni_mira">Кухни мира</a>
						<a href="/content/kulinar/salaty_i_zakuski">Салаты и закуски</a>
						<a href="/content/kulinar/osnovnye_bljuda">Основные блюда</a>
						</td>
					  <td>
						<a  href="/content/kulinar/vypechka_i_deserty">Выпечка и десерты</a>
						<a   href="/content/kulinar/supy">Супы</a>
						<a  href="/content/kulinar/sousy">Соусы</a>
						<a  href="/content/kulinar/napitki">Напитки</a>
						<a   href="/content/kulinar/videorecepty">Видеорецепты</a>
					  </td>
					 </tr>
					</table>
					</div>
				</li>
			</ul>

<div class="contentfield"><!--contentfield-->
