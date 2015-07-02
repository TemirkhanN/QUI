<?php if(!defined('recombo')){exit();} ?>

</div><!--contentfield-->
		<div class="mainfooter">
		<a href="/feedback.html">Связь с нами</a> &emsp; &emsp; &emsp;
		<a href="/sitemap.html">Карта сайта</a> &emsp; &emsp; &emsp;




		</div><!--mainfooter-->

        <!-- The JavaScript -->
		<script type="text/javascript" src="/js/jquery.easing.1.3.js"></script>
        <script type="text/javascript">
            $(function() {
                $('#sdt_menu > li').bind('mouseenter',function(){
					var $elem = $(this);
					$elem.find('img')
						 .stop(true)
						 .animate({
							'width':'127.5px',
							'height':'127.5px',
							'left':'0px'
						 },400,'easeOutBack')
						 .andSelf()
						 .find('.sdt_wrap')
					     .stop(true)
						 .animate({'top':'105px'},500,'easeOutBack')
						 .andSelf()
						 .find('.sdt_active')
					     .stop(true)
						 .animate({'height':'127.5px'},300,function(){
						var $sub_menu = $elem.find('.sdt_box');
						if($sub_menu.length){
							var left = '127.5px';
							if($elem.parent().children().length == $elem.index()+1)
								left = '-382.5px';
							$sub_menu.show().animate({'left':left},200);
						}
					});
				}).bind('mouseleave',function(){
					var $elem = $(this);
					var $sub_menu = $elem.find('.sdt_box');
					if($sub_menu.length)
						$sub_menu.hide().css('left','0px');
					
					$elem.find('.sdt_active')
						 .stop(true)
						 .animate({'height':'0px'},300)
						 .andSelf().find('img')
						 .stop(true)
						 .animate({
							'width':'0px',
							'height':'0px',
							'left':'64px'},400)
						 .andSelf()
						 .find('.sdt_wrap')
						 .stop(true)
						 .animate({'top':'18.75px'},500);
				});
            });
        </script>
        </div><!--mainalign-->
    </body>
</html>
