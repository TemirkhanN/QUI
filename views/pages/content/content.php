<?php

if( $content != null ):
    $this->setTitle($content['headline'].' | Vforme.su');
    $this->setMetaDesc($content['preview']);
    $this->setMetaKeys($content['tags']);
?>
    <div class="content">
            <h1 class="centered">
                <?=$content['headline']?>
            </h1>

        <br/>
        <br/>
        <p class="content-text"><?=strip_tags($content['text'],'<img>')?></p>
        <div class="js-rating content-rating"
             data-target="content"
             data-target-id="<?=$content['id']?>">
        </div>
        <br>
        <br>
        <br>
    </div>

<?
endif;
?>