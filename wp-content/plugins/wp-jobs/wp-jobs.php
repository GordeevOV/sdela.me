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
?>