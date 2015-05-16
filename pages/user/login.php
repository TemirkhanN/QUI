<? if($login_errors):?>
    <div class="alert alert-danger" role="alert">
        <? foreach($login_errors as $key=>$login_error): ?>
            <strong>!</strong>
            <?=$login_error?><br>
        <? endforeach;?>
    </div>
<? endif;?>

<? if(!$user->authorized): ?>

    <? $form = new \app\core\base\FormBuilder(['method'=>'POST', 'id'=>'login-form']); ?>
    <?=$form->begin()?>
    <?=$form->field('text', ['name'=>'login', 'placeholder'=>'Ваш логин'])?><br>
    <?=$form->field('text', ['name'=>'password', 'placeholder'=>'Ваш пароль'])?><br>
    <?=$form->field('submit', ['name'=>'log_in', 'value'=>'Войти', 'class'=>'btn btn-sm btn-success'])?><br>
    <?=$form->end();?>
<? endif; ?>
