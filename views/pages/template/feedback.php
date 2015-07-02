<?php
if(!defined('recombo')){exit();}

$message = '';

if(isset($_POST['submit'])){
	if(!empty($_POST['nickname'])){
		$nickname=htmlspecialchars($_POST['nickname']);
	}
	else{
		$nickname='Инкогнито';
	}
	
	if(!empty($_POST['email'])){
		$email=htmlspecialchars($_POST['email']);
	}
	else{
		$email='noreply@paranoia.today';
	}
	
	if(!empty($_POST['message'])){
		$message=htmlspecialchars($_POST['message']);
	}
	else{
		$error=true;
	}
	
	if(!isset($error)){
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html;  charset=utf-8\r\n";
		$headers .= "Date: ".date("Y-m-d (G:i:s)",time())."\r\n";
		$headers .= "From: \"$nickname\" <$email>\r\n";
		$headers .= "X-Mailer: My Send E-mail\r\n";
		if(mail('noreply@paranoia.today', 'paranoia feedback', $message, $headers)){
		 $sent=true;
		}
	}
}


$PAGE->site['title'] = 'Связь с нами';

$HTML='<style>

#mainBlock{
	clear:left;
	background-color:#eee;
	padding:10px 20px;
}

form{
	padding:10px 40px;
}
label {
    display:block;
    margin-top:20px;
    letter-spacing:2px;
}

input, textarea {
    width:439px;
    height:27px;
    font-size:14px;
    background:#efefef;
    border:1px solid #dedede;
    padding:10px;
    margin-top:3px;
    color:#3a3a3a;
    -moz-border-radius:5px;
    -webkit-border-radius:5px;
    border-radius:5px;
}

input:focus, textarea:focus {
    border:1px solid #97d6eb;
}

textarea{
	height:100px;
}
#submit {
    width:127px;
    height:38px;
    margin-top:20px;
    cursor:pointer;
}

	#submit:hover {
	    opacity:.9;
	}
</style>';


$HTML .= '<div id="mainBlock">';

if(!isset($sent)){
	
	$HTML .= '<form method="POST" action="/feedback.html">

		<label>Ваш никнейм</label>
		<input name="nickname" placeholder="Инкогнито">

		<label>Ваша почта</label>
		<input name="email" type="email" placeholder="Укажите, если хотите получить ответ">

		<label>Ваше сообщение</label>
		<textarea name="message" placeholder="Пожелание, жалоба, обращение">'.$message.'</textarea>
		<br>
		<input id="submit" name="submit" type="submit" value="Отправить">
	</form>';

}
else{
	$HTML .= 'Спасибо за Ваше обращение. Для нас очень важно Ваше мнение.<br><br>';
}

$HTML .= '*Вы также можете связаться с нами через следующие средства связи:<br><br>
		ICQ : 919153<br>
		VK :<a href="https://vk.com/logichna_kak_vilka">Ксения Владимировна</a><br>

</div>';

require_once PATH."/template/header.php";

echo $HTML;

require_once PATH."/template/footer.php";
?>
