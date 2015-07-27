<?php
use app\core\web\Html;
use app\plugins\pretty_date\PrettyDate;

?>


<? $this->layout('main/slider'); ?>

<br>
<?if($contents):?>
    <?
    $count = 0;
    ?>
    <div class="content">

    <? foreach($contents as $content): ?>

        <? if($count%2 === 0):?>
            <div class="row">
        <? endif;?>

        <?$count++;?>

		<div class="col-sm-6 content-preview">
            <div class="col-md-5">
                <a href="/<?=$content['cat']?>/<?=$content['link']?>.html">
                    <img class="img-thumbnail content-preview-image" src="/images/articles/preview/<?=$content['image']?>.jpg" title="<?=Html::encode($content['headline'])?>" alt="<?=Html::encode($content['headline'])?>">
                </a>

                <div class="js-rating content-rating"
                     data-target="content"
                     data-target-id="<?=$content['id']?>">
                </div>
                <div class="content-date"><?=PrettyDate::convert($content['pubdate'], true)?></div>

            </div>
            <div class="col-md-7">
                <div class="content-link">
                    <a href="/<?=$content['cat']?>/<?=$content['link']?>.html"><?=$content['headline']?></a>
                </div>
                <div class="content-preview-text"><?=$content['preview']?></div>
            </div>


        </div><!--content_preview-->
        <? if($count%2 === 0):?>
            </div>
        <? endif;?>


    <? endforeach; ?>
    </div>

    <div class="both-clear"></div>

    <?=(new \app\plugins\pagination\Pagination($elementsOnPage, $totalElements))->pagination()?>



<?endif;?>