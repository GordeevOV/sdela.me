<?php
/*
If you would like to edit this file, copy it to your current theme's directory and edit it there.
Theme My Login will always look in your theme's directory first, before using this default template.
*/
?>
<div class="tml tml-register" id="theme-my-login<?php $template->the_instance(); ?>">
	<?php //$template->the_action_template_message( 'register' ); ?>
	<?php $template->the_errors(); ?>
	<form name="registerform" id="registerform<?php $template->the_instance(); ?>" action="<?php $template->the_action_url( 'register', 'login_post' ); ?>" method="post">
		<?php if ( 'email' != $theme_my_login->get_option( 'login_type' ) ) : ?>
		<p class="tml-user-login-wrap">
			<label for="user_login<?php $template->the_instance(); ?>"><?php _e( 'Username', 'theme-my-login' ); ?></label>
			<input type="text" name="user_login" id="user_login<?php $template->the_instance(); ?>" class="input" value="<?php $template->the_posted_value( 'user_login' ); ?>" size="20" />
		</p>
		<?php endif; ?>

		<p class="tml-user-email-wrap">
			<!--<label for="user_email<?php $template->the_instance(); ?>"><?php _e( 'E-mail', 'theme-my-login' ); ?></label>-->
			<input type="text" name="user_email" id="user_email<?php $template->the_instance(); ?>" class="input" value="<?php $template->the_posted_value( 'user_email' ); ?>" size="20" placeholder="<?php _e( 'Для просмотра подтвердите свой е-mail', 'theme-my-login' ); ?>"/>
		</p>
		<p class="errormsg" id="wrong_email"></p>

		<?php do_action( 'register_form' ); ?>

		<p class="tml-registration-confirmation" id="reg_passmail<?php $template->the_instance(); ?>"><?php echo apply_filters( 'tml_register_passmail_template_message', __( 'Registration confirmation will be e-mailed to you.', 'theme-my-login' ) ); ?></p>

		<p class="tml-submit-wrap">
			<input type="submit" name="wp-submit" id="wp-submit<?php $template->the_instance(); ?>" value="<?php esc_attr_e( 'Отправить', 'theme-my-login' ); ?>" />
			<input type="hidden" name="redirect_to" value="<?php $template->the_redirect_url( 'register' ); ?>" />
			<input type="hidden" name="instance" value="<?php $template->the_instance(); ?>" />
			<input type="hidden" name="action" value="register" />
		</p>
	</form>
	<?php $template->the_action_links( array( 'register' => false ) ); ?>
</div>

<script> //fields validation

	function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
    }

	jQuery('form').submit(function(){
		var errors = 0;
		jQuery('.errormsg').html('');
		jQuery('#wrong_email').css('margin-top', '');
		jQuery('#short_passw').css('margin-top', '');
		jQuery('#wrong_passw').css('margin-top', '');
		
		if (!isValidEmailAddress(jQuery('input[id^="user_email"]').val())) {
			jQuery('#wrong_email').html('Некорректный адрес e-mail');
			jQuery('#wrong_email').css('margin-top', '-20px');
			errors = 1;
		}
		
		if (jQuery('input[id^="user_email"]').val() == '') {
			jQuery('#wrong_email').html('Поле обязательно к заполнению');
			jQuery('#wrong_email').css('margin-top', '-20px');
			errors = 1;
		}
		
		if (jQuery('input[id^="pass1"]').val().length < 8) {
			jQuery('#short_passw').html('Пароль не менее 8 символов');
			jQuery('#short_passw').css('margin-top', '-20px');
			errors = 1;
		}
		
		if (jQuery('input[id^="pass1"]').val() != jQuery('input[id^="pass2"]').val()) {
			jQuery('#wrong_passw').html('Пароли не совпадают');
			jQuery('#wrong_passw').css('margin-top', '-20px');
			errors = 1;
		}
		
		if (errors == 1) {
			return false;	
		}
		
	});
</script>