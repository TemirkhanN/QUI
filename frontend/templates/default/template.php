<?php

use \app\core\web\Html;
$this->registerCss('css/main.stylesheet.css');
$this->setTitle('Женская красота и здоровье - Vforme.su');
$this->setMetaDesc('Быть красивой и здоровой-легко. Уход за кожей лица, рук и тела. Питание и диеты.');
$this->setMetaKeys('Красота,krasota, женщины,девушки,здоровье,видеорецепты,диета,уход за волосами, уход за телом,маски для лица,rhfcjnf');

?>
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

    <? if($this->isCustomTemplate()): ?>

        <?=$this->content()?>

    <? else: ?>
        <header>
            <?=$this->layout()?>
        </header>

        <div id="content">
            <div class="container">
                <?=$this->content()?>
            </div>
        </div>

        <footer>
            <div class="container">
                <span style="color:#eeee60; text-shadow:2px 1px 1px #000; font:20px/normal Franklin Gothic Medium;">&copy;&nbsp;<?php echo date("Y");?>&ensp;<a href="http://vforme.su">Vforme.su</a></span> &emsp; &emsp;


                <!-- Yandex.Metrika informer -->
                <a href="https://metrika.yandex.ru/stat/?id=27030063&amp;from=informer" target="_blank" rel="nofollow">
                    <img src="//bs.yandex.ru/informer/27030063/1_0_D4FACFFF_B4DAAFFF_0_uniques" style="width:80px; height:15px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (уникальные посетители)" onclick="try{Ya.Metrika.informer({i:this,id:27030063,lang:'ru'});return false}catch(e){}"/>
                </a>
                <!-- /Yandex.Metrika informer -->

                <!-- Yandex.Metrika counter -->
                <script type="text/javascript">
                (function (d, w, c) {
                    (w[c] = w[c] || []).push(function() {
                        try {
                            w.yaCounter27030063 = new Ya.Metrika({id:27030063,
                                webvisor:true,
                                clickmap:true,
                                trackLinks:true,
                                accurateTrackBounce:true
                            });
                        } catch(e) { }
                    });

                    var n = d.getElementsByTagName("script")[0],
                        s = d.createElement("script"),
                        f = function () { n.parentNode.insertBefore(s, n); };
                    s.type = "text/javascript";
                    s.async = true;
                    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

                    if (w.opera == "[object Opera]") {
                        d.addEventListener("DOMContentLoaded", f, false);
                    } else { f(); }
                })(document, window, "yandex_metrika_callbacks");
                </script>
                <noscript>
                    <div>
                        <img src="//mc.yandex.ru/watch/27030063" style="position:absolute; left:-9999px;" alt="" />
                    </div>
                </noscript>
                <!-- /Yandex.Metrika counter -->
            </div>
        </footer>

    <? endif; ?>
</body>
</html>