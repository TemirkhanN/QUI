<?php
$navigation = [
    'Configuration' => '/wiki/config/',
    'Routing system' => '/wiki/router/',
    'Core' => '/wiki/core/',
    'Controller' => '/wiki/core/',
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