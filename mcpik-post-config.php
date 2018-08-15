<?php
/*
Plugin Name: McPik Post Custom
Plugin URI: http://www.donnamcmaster.com/
Description: Configuration and admin for custom post types, fields, and edit columns. Based on Piklist.
Version: 00.01.00
Author: Donna McMaster
Author URI: http://www.donnamcmaster.com/
Plugin Type: Piklist
License: GPL2 (see _LICENSE.TXT)

05-Jun-2016	DMc	customized for Coloma.com
10-Mar-2018	DMc	customized for ColomaShuttle.com
09-Aug-2018	DMc	convert classes from dynamic to static
*/

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *	Dummy error log function for sites without mcw-debug-log plugin
 */
if ( !function_exists( 'mcw_log' ) ) {
	function mcw_log ( $s, $level='info' ) {
	}
}

define( 'MCPK_PT_PLUGIN_URL', plugins_url( '' , __FILE__ ) );

define( 'MCPK_PT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'MCPK_PT_CLASS_PATH', MCPK_PT_PLUGIN_PATH . 'post-types' );
define( 'MCPK_PT_INCLUDES_PATH', MCPK_PT_PLUGIN_PATH . 'includes' );

define( 'MCPK_PT_PREFIX', 'McPik_Post_Type_' );


/**
 *	Init
 *	- plugin depends on Piklist; make sure it's active
 */
add_action( 'init', function () {
	if ( is_admin() ) {
		include_once( MCPK_PT_INCLUDES_PATH . '/class-piklist-checker.php' );
		if ( !piklist_checker::check( __FILE__ ) ) {
			return;
		}
	}
	include_once( MCPK_PT_INCLUDES_PATH.'/class-mcpik-post-config.php' );
}, 0 );


/**
 *	Enqueue Admin Stylesheet
 */
add_action( 'admin_enqueue_scripts', function () {
	wp_enqueue_style( 'mcpk-pt-admin', MCPK_PT_PLUGIN_URL.'/parts/css/mcpk-pt-admin.css' );
});
