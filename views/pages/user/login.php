<style>
    body {
        padding-bottom: 40px;
        background-color: #eee;
    }

    .form-signin {
        max-width: 330px;
        padding: 15px;
        margin: 0 auto;
    }
    .form-signin .form-signin-heading,
    .form-signin .checkbox {
        margin-bottom: 10px;
    }
    .form-signin .checkbox {
        font-weight: normal;
    }
    .form-signin .form-control {
        position: relative;
        height: auto;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        padding: 10px;
        font-size: 16px;
    }
    .form-signin .form-control:focus {
        z-index: 2;
    }
    .form-signin input[type="email"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }
    .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

</style>



<? if($login_errors):?>
    <div class="alert alert-danger" role="alert">
        <? foreach($login_errors as $login_error): ?>
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <?=$login_error?><br>
        <? endforeach;?>
    </div>
<? endif;?>


<?php
$loginForm = new core\web\Form(['method'=>'POST', 'class'=>'form-signin']);
?>

<?=$loginForm->begin()?>
    <h2 class="form-signin-heading">Authorization</h2>
    <?=$loginForm->field('text', ['name'=>'login', 'class'=>'form-control', 'id'=>'inputMail', 'placeholder'=>'your login', 'required'=>true, 'autofocus'=>'true'])?>
    <?=$loginForm->field('password', ['name'=>'password', 'id'=>'inputPassword', 'class'=>'form-control', 'placeholder'=>'*******', 'required'=>true])?>
    <?=$loginForm->field('submit', ['name'=>'log_in', 'class'=>'btn btn-lg btn-primary btn-block', 'value'=>'Login'])?>
<?=$loginForm->end()?>