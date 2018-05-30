<?php
/*
Plugin Name: McPik Post Custom
Plugin URI: http://www.donnamcmaster.com/
Description: Configuration and admin for post types, custom fields, and edit columns. Based on Piklist
Version: 00.01.00
Author: Donna McMaster
Author URI: http://www.donnamcmaster.com/
Plugin Type: Piklist
License: GPL2 (see _LICENSE.TXT)

05-Jun-2016	DMc	customized for Coloma.com
10-Mar-2018	DMc	customized for ColomaShuttle.com
29-May-2018	DMc	renamed from mcpik-post-types to mcpik-post-custom
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
		include_once( MCPK_SHTL_INCLUDES_PATH . '/class-piklist-checker.php' );
		if ( !piklist_checker::check( __FILE__ ) ) {
			return;
		}
	}
	global
		$mcpk_pt_handlers;

	$mcpk_pt_handlers = array();
	include_once( MCPK_PT_INCLUDES_PATH.'/class-post-columns.php' );

	// get a list of post type class files
	$post_types_list = piklist::get_directory_list( MCPK_PT_CLASS_PATH );

	// create handlers
	foreach ( $post_types_list as $file ) {
		$post_type = substr_replace( substr( $file, 0, -4 ), '', 0, 10 );
		$class_name = MCPK_PT_PREFIX . ucfirst( $post_type );
		include_once( MCPK_PT_CLASS_PATH .'/'. $file );
		$mcpk_pt_handlers[$post_type] = new $class_name();
	}
});


/**
 *	Enqueue Admin Stylesheet
 */
add_action( 'admin_enqueue_scripts', function () {
	wp_enqueue_style( 'mcpk-pt-admin', MCPK_PT_PLUGIN_URL.'/parts/css/mcpk-pt-admin.css' );
});


// replace later with this?
//add_filter( 'piklist_assets', 'mcw_post_custom_assets' );
function mcw_post_custom_assets ( $assets ) {
	array_push( $assets['styles'], array(
		'handle' => 'mcw-post-custom',
		'src' => piklist::$addons['mcw-piklist-custom']['url'] . '/add-ons/piklist-demos/parts/css/piklist-demo.css',
		'media' => 'screen, projection',
		'enqueue' => true,
		'admin' => true,
	));
	return $assets;
}


/**
 *	Custom Admin Columns
 */
if ( is_admin() ) {
	add_action( 'manage_posts_custom_column', 'mcpk_pt_custom_column', 10, 2);
	add_action( 'manage_pages_custom_column', 'mcpk_pt_custom_column', 10, 2);
	add_action( 'manage_media_custom_column', 'mcpk_pt_media_custom_column', 10, 2);
}
function mcpk_pt_custom_column ( $column_name, $id ) {
	global $mcpk_pt_handlers;
	$post_type = get_post_type( $id );
	if ( array_key_exists( $post_type, $mcpk_pt_handlers ) ) {
		$mcpk_pt_handlers[$post_type]->custom_column( $column_name, $id );
	}
}
function mcpk_pt_media_custom_column( $column_name, $id ) {
	global $mcpk_pt_handlers;
	$mcpk_pt_handlers['attachment']->custom_column( $column_name, $id );
}
?>