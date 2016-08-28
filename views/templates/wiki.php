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

<style>
    pre{
        font-size:14px;
    }
</style>
<div id="content">

    <div class="container">
        <div class="col-sm-3">
            <?php
            $navigation = [
                'Configuration' => '/wiki/config/',
                'Routing system' => '/wiki/router/',
                'Core' => '/wiki/qui/',
                'Controller' => '/wiki/qui/',
                'View' => '/wiki/core/',
                'Global constants' => '/wiki/constants/'
            ];

            $activeLink = $_SERVER['REQUEST_URI'];
            ?>

            <style>
                .list-group a.active-link{
                    background-color: rgba(51, 122, 183, 0.7);
                    color: white;
                }
            </style>


            <div class="list-group">
                <a href="/" class="list-group-item active ">
                    <span class="glyphicon glyphicon-flash"></span>
                    QUINTESSENCE v 1.0
                </a>
                <?php foreach($navigation as $linkName=>$href): ?>
                    <a href="<?=$href?>" class="list-group-item<?=$activeLink==$href ? ' active-link' : ''?>"><?=$linkName?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-sm-9">
            <?=$this->content()?>
        </div>
    </div>
</div>
<br>

<footer class="container">
    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title">&copy; Quintessence <?=date('Y')?> all rights corrupted</h3>
        </div>
        <div class="panel-body">

            special thx for <a href="https://highlightjs.org/">highlightjs</a>
        </div>
    </div>
</footer>
</body>
</html>