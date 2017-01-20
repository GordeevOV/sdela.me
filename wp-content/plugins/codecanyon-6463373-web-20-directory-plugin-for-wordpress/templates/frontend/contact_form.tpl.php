<form method="POST" action="<?php the_permalink($listing->post->ID); ?>#contact-tab" id="w2dc_contact_form">
	<input type="hidden" name="listing_id" id="w2dc_listing_id" value="<?php echo $listing->post->ID; ?>" />
	<input type="hidden" name="contact_nonce" id="w2dc_contact_nonce" value="<?php print wp_create_nonce('w2dc_contact_nonce'); ?>" />
	<h3><?php _e('Send message to listing owner', 'W2DC'); ?></h3>
	<h5 id="w2dc_contact_warning" style="display: none; color: red;"></h5>
	<div class="w2dc-contact-form">
		<?php if (is_user_logged_in()): ?>
		<p>
			<?php printf(__('You are currently logged in as %s. Your message will be sent using your logged in name and email.', 'W2DC'), wp_get_current_user()->user_login); ?>
			<input type="hidden" name="contact_name" id="w2dc_contact_name" />
			<input type="hidden" name="contact_email" id="w2dc_contact_email" />
		</p>
		<?php else: ?>
		<p>
			<label for="contact_name"><?php _e('Contact Name', 'W2DC'); ?><span class="w2dc-red-asterisk">*</span></label>
			<input type="text" name="contact_name" id="w2dc_contact_name" class="w2dc-form-control" value="<?php echo esc_attr(w2dc_getValue($_POST, 'contact_name')); ?>" size="35" />
		</p>
		<p>
			<label for="contact_email"><?php _e("Contact Email", "W2DC"); ?><span class="w2dc-red-asterisk">*</span></label>
			<input type="text" name="contact_email" id="w2dc_contact_email" class="w2dc-form-control" value="<?php echo esc_attr(w2dc_getValue($_POST, 'contact_email')); ?>" size="35" />
		</p>
		<?php endif; ?>
		<p>
			<label for="contact_message"><?php _e("Your message", "W2DC"); ?><span class="w2dc-red-asterisk">*</span></label>
			<textarea name="contact_message" id="w2dc_contact_message" class="w2dc-form-control" rows="6"><?php echo esc_textarea(w2dc_getValue($_POST, 'contact_message')); ?></textarea>
		</p>
		
		<?php echo w2dc_recaptcha(); ?>
		
		<input type="submit" name="submit" class="w2dc-send-message-button w2dc-btn w2dc-btn-primary" value="<?php esc_attr_e('Send message', 'W2DC'); ?>" />
	</div>
</form>