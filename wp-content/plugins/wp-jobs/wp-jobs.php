<?php
/*
Plugin Name: WP Jobs
Description: Job posting Plugin
Version: 1.0
Author: Oleg V. Gordeev
*/

define('WP_JOBS_DIR', plugin_dir_path(__FILE__));
define('WP_JOBS_URL', plugin_dir_url(__FILE__));

register_activation_hook(__FILE__, 'wp_jobs_activation');
register_deactivation_hook(__FILE__, 'wp_jobs_deactivation');

function wp_jobs_activation() {
 
    // действие при активации
    register_uninstall_hook(__FILE__, 'wp_jobs_uninstall');
    //remove_role( 'subscriber' );
    //remove_role( 'author' );
    add_role('moderator_role', 'Модератор', array( 'read' => true ) );
}
 
function wp_jobs_deactivation() {
    // при деактивации
}

function wp_jobs_uninstall(){
 
    //действие при удалении
}

//Удаляем лишнее из профиля

function admin_del_options() {
	global $_wp_admin_css_colors;
	global $wp_rich_edit_exists;
	 
	$_wp_admin_css_colors = 0;
	$wp_rich_edit_exists = 0;
}
 
add_action(‘admin_head’, ‘admin_del_options’);

function true_remove_personal_options(){
	echo "\n" . '<script type="text/javascript">
	jQuery(document).ready(function($) {
	$(\'form#your-profile > h2:first\').hide();
	$(\'form#your-profile > table:first\').hide();
	$(\'form#your-profile tr.user-url-wrap\').hide();
	$(\'form#your-profile\').show(); });
	</script>' . "\n";
}
 
add_action('admin_head', 'true_remove_personal_options');

//Добавляем поля в профиль пользователя
add_filter('user_contactmethods', 'my_user_contactmethods');

function my_user_contactmethods($user_contactmethods)
{
    $user_contactmethods['tel'] = '<b>Телефон</b>'; 
    $user_contactmethods['tel2'] = '<b>Телефон 2</b>';
    $user_contactmethods['addr'] = '<b>Адрес</b>'; 
    $user_contactmethods['birthdate'] = '<b>Дата рождения</b>';  
	$user_contactmethods['education'] = '<b>Образование</b>';
	$user_contactmethods['spec'] = '<b>Специальность</b>';
	$user_contactmethods['experience'] = '<b>Опыт работы</b>';
	$user_contactmethods['job_category'] = '<b>Виды деятельности</b>';
	$user_contactmethods['facebook'] = '<b>Facebook</b>';
	$user_contactmethods['vkontakte'] = '<b>ВКонтакте</b>';
	$user_contactmethods['twitter'] = '<b>Twitter</b>';
	$user_contactmethods['linkedin'] = '<b>LinkedIn</b>';
	$user_contactmethods['instagram'] = '<b>Istagram</b>';
	$user_contactmethods['skype'] = '<b>Skype</b>';
	$user_contactmethods['viber'] = '<b>Viber</b>';
	$user_contactmethods['whatsapp'] = '<b>WhatsApp</b>';

    return $user_contactmethods;
}
?>