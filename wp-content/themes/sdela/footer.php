<div class="dmbs-footer-wrap">
	<div class="container dmbs-container">
	    <div class="dmbs-footer">
	        <?php global $dm_settings;?>
			<div class="row footer-top">
				<div class="col-md-3 col-sm-6 col-xs-12 footer-logo">
					<img src="<?php echo get_template_directory_uri();?>/img/logo-footer.jpg" alt="" />
					<h1><?php bloginfo( 'name' ); ?></h1>
                	<h4 class="custom-header-text-color"><?php bloginfo( 'description' ); ?></h4>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-12 footer-menu">
					<?php  dynamic_sidebar( 'footer-menu1' ); ?>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-12 footer-menu">
					<?php  dynamic_sidebar( 'footer-menu2' ); ?>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-12 footer-menu">
					<?php  dynamic_sidebar( 'footer-menu3' ); ?>
				</div>
			</div>
			
			<div class="row footer-social">
				<div class="col-md-12">
					<?php 
					if ($dm_settings['facebook']) { ?>
						<a href="<?php echo $dm_settings['facebook']?>"><i class="fa fa-facebook"  aria-hidden="true"></i></a>
					<?php 
					}
					?>
					<?php 
					if ($dm_settings['twitter']) { ?>
						<a href="<?php echo $dm_settings['twitter']?>"><i class="fa fa-twitter"  aria-hidden="true"></i></a>
					<?php 
					}
					?>
					<?php 
					if ($dm_settings['gplus']) { ?>
						<a href="<?php echo $dm_settings['gplus']?>"><i class="fa fa-google-plus"  aria-hidden="true"></i></a>
					<?php 
					}
					?>
					<?php 
					if ($dm_settings['youtube']) { ?>
						<a href="<?php echo $dm_settings['youtube']?>"><i class="fa fa-youtube-play"  aria-hidden="true"></i></a>
					<?php 
					}
					?>
					<?php 
					if ($dm_settings['pinterest']) { ?>
						<a href="<?php echo $dm_settings['pinterest']?>"><i class="fa fa-pinterest-p"  aria-hidden="true"></i></a>
					<?php 
					}
					?>
					<?php 
					if ($dm_settings['instagram']) { ?>
						<a href="<?php echo $dm_settings['instagram']?>"><i class="fa fa-instagram"  aria-hidden="true"></i></a>
					<?php 
					}
					?>
				</div>
			</div>
			
	    </div>

	</div>

</div>
<!-- end main container -->

<?php wp_footer(); ?>

<!--Всплывающее окно регистрации-->
<?php if ( !is_user_logged_in() ) {?>

<div class="b-popup">
    <div class="b-popup-content" id="popup">
    	<h3>Добро пожаловать.</h3>
    	<p>Все данные доступны только зарегистрированым пользователям.</p>
    	<p>Попробуйте бесплатно в течение 14 дней. Без риска и кредитных карт.</p>
        <?php  dynamic_sidebar( 'register-popup' ); ?>
        <div class="close-popup">
        	<!--<img src="<?php echo get_template_directory_uri();?>/img/cross.png" />-->
        	<i class="fa fa-times"></i>
    	</div>
    </div>
    <div class="close-popup">
    </div>
</div>

<!--Всплывающее окно логина-->

<div class="l-popup">
    <div class="l-popup-content" id="l-popup">
    	<h3>Добро пожаловать.</h3>
        <?php  dynamic_sidebar( 'login-popup' ); ?>
        <div class="close-l-popup">
        	<!--<img src="<?php echo get_template_directory_uri();?>/img/cross.png" />-->
        	<i class="fa fa-times"></i>
    	</div>
    </div>
</div>

<script>
	function PopupShow() {
		jQuery('.b-popup').show();
		jQuery('.b-popup-content').show();
		jQuery('.close-popup').show();
	}
	
	function PopupHide() {
		jQuery('.b-popup').hide();
		jQuery('.b-popup-content').hide();
		jQuery('.close-popup').hide();
	}
	
	function LPopupShow() {
		jQuery('.l-popup').show();
		jQuery('.l-popup-content').show();
		jQuery('.close-l-popup').show();
	}
	
	function LPopupHide() {
		jQuery('.l-popup').hide();
		jQuery('.l-popup-content').hide();
		jQuery('.close-l-popup').hide();
	}
	
	jQuery('.close-popup').click(function(){
		PopupHide();
	});
	
	jQuery('.close-l-popup').click(function(){
		LPopupHide();
	});
	
	jQuery(function($){
	$(document).mouseup(function (e){ // событие клика по веб-документу
		var div = $("#popup"); // тут указываем ID элемента
		var div2 = $("#l-popup");
		if (!div.is(e.target) // если клик был не по нашему блоку
		    && div.has(e.target).length === 0) { // и не по его дочерним элементам
			PopupHide(); // скрываем его
		}
		if (!div2.is(e.target) // если клик был не по нашему блоку
		    && div2.has(e.target).length === 0) { // и не по его дочерним элементам
			LPopupHide(); // скрываем его
		}
	});
});
	
	
	
</script>
<?php } ?>
</body>
</html>