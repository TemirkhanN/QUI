<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Author"  content="Temirkhan" >
    <title><?=$this->title()?></title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <?=$this->headers()?>
</head>
<body>

<div id="content">
    <div class="container">

        <?=$this->content()?>

    </div>
</div>
</body>
</html>