<?php
/*
If you would like to edit this file, copy it to your current theme's directory and edit it there.
Theme My Login will always look in your theme's directory first, before using this default template.
*/
?>
<div class="tml tml-profile" id="theme-my-login<?php $template->the_instance(); ?>">
	<?php $template->the_action_template_message( 'profile' ); ?>
	<?php $template->the_errors(); ?>
	<form id="your-profile" action="<?php $template->the_action_url( 'profile', 'login_post' ); ?>" method="post" enctype=”multipart/form-data”>
	
<div class="row">
	
	<div class="col-md-3 col-sm-12"> 
	
	
		<?php wp_nonce_field( 'update-user_' . $current_user->ID ); ?>
		<p>
			<input type="hidden" name="from" value="profile" />
			<input type="hidden" name="checkuser_id" value="<?php echo $current_user->ID; ?>" />
		</p>

		<h3 style="display: none;"><?php _e( 'Personal Options', 'theme-my-login' ); ?></h3>

		<table class="tml-form-table" style="display: none;">
		<tr class="tml-user-admin-bar-front-wrap">
			<th><label for="admin_bar_front"><?php _e( 'Toolbar', 'theme-my-login' )?></label></th>
			<td>
				<label for="admin_bar_front"><input type="checkbox" name="admin_bar_front" id="admin_bar_front" value="1"<?php checked( _get_admin_bar_pref( 'front', $profileuser->ID ) ); ?> />
				<?php _e( 'Show Toolbar when viewing site', 'theme-my-login' ); ?></label>
			</td>
		</tr>
		<?php do_action( 'show_user_profile', $profileuser ); ?>
		<?php do_action( 'personal_options', $profileuser ); ?>
		</table>

		<?php do_action( 'profile_personal_options', $profileuser ); ?>
	</div>
	
	<div class="col-md-9 col-sm-12">
		<!--<h3><?php _e( 'Name', 'theme-my-login' ); ?></h3>-->

		<table class="tml-form-table profile-table">
		<!--<tr class="tml-user-login-wrap">
			<th><label for="user_login"><?php _e( 'Username', 'theme-my-login' ); ?></label></th>
			<td><input type="text" name="user_login" id="user_login" value="<?php echo esc_attr( $profileuser->user_login ); ?>" disabled="disabled" class="regular-text" /> <span class="description"><?php _e( 'Usernames cannot be changed.', 'theme-my-login' ); ?></span></td>
		</tr>-->
		<input type="hidden" name="user_login" id="user_login" value="<?php echo esc_attr( $profileuser->user_login ); ?>" disabled="disabled" class="regular-text" />
		
		<tr class="tml-last-name-wrap">
			<td class="first"><label for="last_name"><?php _e( 'Last Name', 'theme-my-login' ); ?></label></td>
			<td class="last"><input type="text" name="last_name" id="last_name" value="<?php echo esc_attr( $profileuser->last_name ); ?>" class="regular-text" /></td>
		</tr>

		<tr class="tml-first-name-wrap">
			<td class="first"><label for="first_name"><?php _e( 'Имя и Отчество', 'theme-my-login' ); ?></label></td>
			<td class="last"><input type="text" name="first_name" id="first_name" value="<?php echo esc_attr( $profileuser->first_name ); ?>" class="regular-text" /></td>
		</tr>

		<!--<tr class="tml-nickname-wrap">
			<td><label for="nickname"><?php _e( 'Nickname', 'theme-my-login' ); ?> <span class="description"><?php _e( '(required)', 'theme-my-login' ); ?></span></label></td>
			<td><input type="text" name="nickname" id="nickname" value="<?php echo esc_attr( $profileuser->nickname ); ?>" class="regular-text" /></td>
		</tr>-->
		<input type="hidden" name="nickname" id="nickname" value="<?php echo esc_attr( $profileuser->nickname ); ?>" class="regular-text" />
		

		<tr class="tml-display-name-wrap">
			<td class="first"><label for="display_name"><?php _e( 'Display name publicly as', 'theme-my-login' ); ?></label></td>
			<td class="last">
				<select name="display_name" id="display_name">
				<?php
					$public_display = array();
					//$public_display['display_nickname']  = $profileuser->nickname;
					//$public_display['display_username']  = $profileuser->user_login;

					if ( ! empty( $profileuser->first_name ) )
						$public_display['display_firstname'] = $profileuser->first_name;

					if ( ! empty( $profileuser->last_name ) )
						$public_display['display_lastname'] = $profileuser->last_name;

					if ( ! empty( $profileuser->first_name ) && ! empty( $profileuser->last_name ) ) {
						$public_display['display_firstlast'] = $profileuser->first_name . ' ' . $profileuser->last_name;
						$public_display['display_lastfirst'] = $profileuser->last_name . ' ' . $profileuser->first_name;
					}

					//if ( ! in_array( $profileuser->display_name, $public_display ) )// Only add this if it isn't duplicated elsewhere
					//	$public_display = array( 'display_displayname' => $profileuser->display_name ) + $public_display;

					$public_display = array_map( 'trim', $public_display );
					$public_display = array_unique( $public_display );

					foreach ( $public_display as $id => $item ) {
				?>
					<option <?php selected( $profileuser->display_name, $item ); ?>><?php echo $item; ?></option>
				<?php
					}
				?>
				</select>
			</td>
		</tr>

		<tr class="tml-user-email-wrap">
			<td class="first"><label for="email"><?php _e( 'E-mail ', 'theme-my-login' ); ?> <span class="description"><?php _e( '(required)', 'theme-my-login' ); ?></span></label></td>
			<td class="last"><input type="text" name="email" id="email" value="<?php echo esc_attr( $profileuser->user_email ); ?>" class="regular-text" /></td>
			<?php
			$new_email = get_option( $current_user->ID . '_new_email' );
			if ( $new_email && $new_email['newemail'] != $current_user->user_email ) : ?>
			<div class="updated inline">
			<p><?php
				printf(
					__( 'There is a pending change of your e-mail to %1$s. <a href="%2$s">Cancel</a>', 'theme-my-login' ),
					'<code>' . $new_email['newemail'] . '</code>',
					esc_url( self_admin_url( 'profile.php?dismiss=' . $current_user->ID . '_new_email' ) )
			); ?></p>
			</div>
			<?php endif; ?>
		</tr>

		<tr class="tml-user-url-wrap">
			<td class="first"><label for="url"><?php _e( 'Website', 'theme-my-login' ); ?></label></td>
			<td class="last"><input type="text" name="url" id="url" value="<?php echo esc_attr( $profileuser->user_url ); ?>" class="regular-text code" /></td>
		</tr>

		<?php
			foreach ( wp_get_user_contact_methods() as $name => $desc ) {
			
			if ($name == 'birthdate') {
				echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
			}
			
			if ($name == 'facebook') {
				echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
				echo "<tr><td><label>Мои профили в социальных сетях</label></td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
			}
		
		?>
		
		
		<tr class="tml-user-contact-method-<?php echo $name; ?>-wrap">
			<td class="first"><label for="<?php echo $name; ?>"><?php echo apply_filters( 'user_'.$name.'_label', $desc ); ?></label></td>
			<td class="last"><input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr( $profileuser->$name ); ?>" class="regular-text" /></td>
		</tr>
		<?php
			}
		?>
		</table>
		<br /><br />
		<!--<h3><?php _e( 'About Yourself', 'theme-my-login' ); ?></h3>-->

		<table class="tml-form-table profile-table">
		<tr class="tml-user-description-wrap">
			<!--<th><label for="description"><?php _e( 'Biographical Info', 'theme-my-login' ); ?></label></th>-->
			<td><textarea name="description" id="description" rows="5" cols="30" placeholder="<?php _e( 'Оставить свою заметку (видно только Вам)', 'theme-my-login' ); ?>"><?php echo esc_html( $profileuser->description ); ?></textarea><br />
			<!--<span class="description"><?php _e( 'Share a little biographical information to fill out your profile. This may be shown publicly.', 'theme-my-login' ); ?></span>-->
			</td>
		</tr>

		<?php
		$show_password_fields = apply_filters( 'show_password_fields', true, $profileuser );
		if ( $show_password_fields ) :
		?>
		</table>
		
		
		<br /><br />
		<!--<h3><?php _e( 'Account Management', 'theme-my-login' ); ?></h3>-->
		<table class="tml-form-table profile-table">
		<tr id="password" class="user-pass1-wrap">
			<td class="first"><label for="pass1"><?php _e( 'New Password', 'theme-my-login' ); ?></label></td>
			<td class="last">
				<input class="hidden" value=" " /><!-- #24364 workaround -->
				<button type="button" class="button button-secondary wp-generate-pw hide-if-no-js"><?php _e( 'Generate Password', 'theme-my-login' ); ?></button>
				<div class="wp-pwd hide-if-js">
					<span class="password-input-wrapper">
						<input type="password" name="pass1" id="pass1" class="regular-text" value="" autocomplete="off" data-pw="<?php echo esc_attr( wp_generate_password( 24 ) ); ?>" aria-describedby="pass-strength-result" />
					</span>
					<div style="display:none" id="pass-strength-result" aria-live="polite"></div>
					<button type="button" class="button button-secondary wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Hide password', 'theme-my-login' ); ?>">
						<span class="dashicons dashicons-hidden"></span>
						<span class="text"><?php _e( 'Hide', 'theme-my-login' ); ?></span>
					</button>
					<button type="button" class="button button-secondary wp-cancel-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Cancel password change', 'theme-my-login' ); ?>">
						<span class="text"><?php _e( 'Cancel', 'theme-my-login' ); ?></span>
					</button>
				</div>
			</td>
		</tr>
		<tr class="user-pass2-wrap hide-if-js">
			<td class="first" scope="row"><label for="pass2"><?php _e( 'Repeat New Password', 'theme-my-login' ); ?></label></td>
			<td class="last">
			<input name="pass2" type="password" id="pass2" class="regular-text" value="" autocomplete="off" />
			<p class="description"><?php _e( 'Type your new password again.', 'theme-my-login' ); ?></p>
			</td>
		</tr>
		<tr class="pw-weak">
			<td class="first"><label><?php _e( 'Confirm Password', 'theme-my-login' ); ?></label></td>
			<td class="last weakpass">
				<label>
					<input type="checkbox" name="pw_weak" class="pw-checkbox" />
					<?php _e( 'Confirm use of weak password', 'theme-my-login' ); ?>
				</label>
			</td>
		</tr>
		<?php endif; ?>

		</table>

		<?php //do_action( 'show_user_profile', $profileuser ); ?>

		<p class="tml-submit-wrap">
			<input type="hidden" name="action" value="profile" />
			<input type="hidden" name="instance" value="<?php $template->the_instance(); ?>" />
			<input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr( $current_user->ID ); ?>" />
			<input type="submit" class="button-primary btn btn-primary" value="<?php esc_attr_e( 'Изменить данные и отправить на модерацию', 'theme-my-login' ); ?>" name="submit" id="submit" />
		</p>
		
	</div>
</div>
	</form>
</div>
