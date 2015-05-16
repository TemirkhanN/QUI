<?php use app\core\App;?>


<? if($profile && !$errors): ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <? if($profile->id === $user->data['id']):?>
                    <?=App::t('your profile')?>
                <? else: ?>
                    <?=App::t('profile user')?> <?=$profile->name?>
                <?endif;?>
            </h3>
        </div>
        <div class="panel-body">
            <img class="img-thumbnail" src="http://cs4647.vk.me/u27216604/video/l_27c692be.jpg" width="200" alt="Аватар"><br>
            Ид пользователя: <?=$profile->id?><br>
            Логин пользователя: <?=$profile->login?><br>
            Дата регистрации: <?=$profile->regdate?><br>
        </div>
    </div>

    <? if($profile->id === $user->data['id']):?>
        <a href="/logout"><?=App::t('logout')?></a>
    <?endif;?>

<? else: ?>

    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title">Ошибка</h3>
        </div>
        <div class="panel-body">
            <?if($errors):?>
                <? foreach($errors as $key=>$error): ?>
                    <strong>!</strong>
                    <?=$error?><br>
                <? endforeach;?>
            <?else: ?>
                Произошла непредвиденная ошибка
            <?endif;?>
        </div>
    </div>


<? endif;