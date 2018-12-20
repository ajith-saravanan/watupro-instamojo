<?php
/*
Plugin Name: WatuPRO Integration for InstaMojo
Plugin URI: 
Description: Lets you process payments made through https://www.instamojo.com/
Author: Kiboko Labs
Version: 0.6
Author URI: http://calendarscripts.info/
License: GPLv2 or later
*/

define( 'WATUPROMOJO_PATH', dirname( __FILE__ ) );
define( 'WATUPROMOJO_RELATIVE_PATH', dirname( plugin_basename( __FILE__ )));
define( 'WATUPROMOJO_URL', plugin_dir_url( __FILE__ ));

include(WATUPROMOJO_PATH.'/controllers/shortcodes.php');
include(WATUPROMOJO_PATH.'/controllers/actions.php');
include(WATUPROMOJO_PATH.'/controllers/filters.php');

register_activation_hook( __FILE__, 'watupromojo_activate' );
add_action('init', 'watupromojo_init');

function watupromojo_activate() {
	// let's not use this for now and see how things work only with using sessions	
	global $user_ID, $wpdb;
	watupromojo_init();
		
	// create database tables or add DB fields
}

function watupromojo_init() {
	global $wpdb;
	if (!session_id()) @session_start();
	
	// define constants for table names, if any	
   // define('WATUPROCUSTOM_MYTABLE', $wpdb->prefix.'watuprocustom_mytable');
	
	// typocally you'll want to add at least jQuery
	wp_enqueue_script('jquery');
   
   // add custom admin menu entries if any
	add_action('watupro_admin_menu', 'watupromojo_menu');
	
	// add custom shortcodes here. This will call the shortcode handler in controller/shortcodes.php
	add_shortcode('watupromojo-button', array('WatuPROMojoShortcodes', 'button'));
		
	// add custom action handlers if any
	add_action('template_redirect', array('WatuPROMojoActions', 'verify_payment'));
}

function watupromojo_menu() {
	add_submenu_page('watupro_exams', 'InstaMojo Integration', 'InstaMojo Integration', 'manage_options', 'watupromojo_options', 'watupromojo_options');
}

// manage instamojo settings
function watupromojo_options() {
	if(!empty($_POST['ok'])) {
		$options = array();
		$options['api_key'] = $_POST['api_key'];
		$options['token'] = $_POST['token'];
		$options['button_text'] = $_POST['button_text'];
		$options['link'] = $_POST['link'];
		// names of the two custom field in the Instamojo which will serve us to pass the information about exam / bundle and ID 
		$options['field_item_id'] = $_POST['field_item_id']; 
		$options['field_item_type'] = $_POST['field_item_type'];
		
		update_option('watupromojo_options', $options);
	}
	
	$options = get_option('watupromojo_options');
	
	include(WATUPROMOJO_PATH."/views/options.html.php");
} // end options