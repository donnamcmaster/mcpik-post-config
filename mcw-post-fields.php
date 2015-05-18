<?php
/*
Plugin Name: McWebby Custom Post Fields
Plugin URI: http://www.donnamcmaster.com/
Description: Object-oriented post types configuration and admin; supplements Piklist
Version: 00.02.01
Author: Donna McMaster
Author URI: http://www.donnamcmaster.com/
License: GPL2 (see _LICENSE.TXT)
*/

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MCW_CPF_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'MCW_CPF_PLUGIN_URL', plugins_url( '' , __FILE__ ) );

define( 'MCW_CPF_PREFIX', 'mcw_PostFields_' );
define( 'MCW_CPF_FOLDER', '/mcw-post-fields' );

define( 'MCW_CPF_CLASS_PATH', MCW_CPF_PLUGIN_PATH . 'class' );
define( 'MCW_CPF_CONFIG_FILE', '/config.php' );


/**
 *	Dummy error log function for sites without mcw-debug-log plugin
 */
if ( !function_exists( 'mcw_log' ) {
	function mcw_log ( $s, $level='info' ) {
	}
}


/**
 *	Locates the template file for the given class name
 *	Returns file information
 *	Loads the file (include_once) if $load is true
 */
function locate_post_type_file ( $class_name, $load=false ) {
	if ( !$class_name ) {
		return false;
	}

	$custom_class_path = STYLESHEETPATH . MCW_CPF_FOLDER;
	$template_class_path = TEMPLATEPATH . MCW_CPF_FOLDER;

	$located = '';
	$class_file =  '/' . $class_name . '.php';
	if ( file_exists( $custom_class_path . $class_file ) ) {
		$located = $custom_class_path . $class_file;
	} else if ( file_exists( $template_class_path . $class_file ) ) {
		$located = $template_class_path . $class_file;
	} else if ( file_exists( MCW_CPF_CLASS_PATH . $class_file ) ) {
		$located = MCW_CPF_CLASS_PATH . $class_file;
	}
	if ( !$located ) {
		mcw_log( "locate_post_type_file failed: tried<br>'$custom_class_path$class_file', <br>'$template_class_path$class_file', <br>'".MCW_CPF_CLASS_PATH."$class_file'" );
	}
	if ( $load && $located ) {
		include_once( $located );
	}
	return $located;
}


/**
 *	function mcw_cpf_init
 *	- creates handlers for post types
 */
if ( is_admin() ) {
	add_action( 'init', 'mcw_cpf_init', 11 );
}
function mcw_cpf_init() {

	global
		$mcw_cpf_post_types,
		$mcw_cpf_handlers;

	$mcw_cpf_handlers = array();

	$config_file = STYLESHEETPATH . MCW_CPF_FOLDER . MCW_CPF_CONFIG_FILE; 
	if ( !@include_once( $config_file ) ) {
		mcw_log( "mcw_cpf_init: no config" );
		return;
	}

	if ( !class_exists( 'mcw_PostFields' ) ) {
		include_once 'includes/mcw_PostFields.php';
	}

	// create handlers for built-in types
	$built_in_types = array( 'post', 'page', 'attachment' );
	foreach ( $built_in_types as $type ) {
		mcw_PostFields::create_handler( $type );
	}

	// create handlers for custom types
	if ( $mcw_cpf_post_types ) {
		foreach ( $mcw_cpf_post_types as $type ) {
			mcw_PostFields::create_handler( $type );
		}
	}
}


/**
 *	Enqueue Admin Stylesheet
 */
add_action( 'admin_enqueue_scripts', 'mcw_enqueue_cpf_admin_scripts' );
function mcw_enqueue_cpf_admin_scripts () {
	wp_enqueue_style( 'mcw-cpf-admin', MCW_CPF_PLUGIN_URL.'/css/mcw-cpf-admin.css' );
}


/**
 *	Custom Admin Columns
 */

if ( is_admin() ) {
	add_action( 'manage_posts_custom_column', 'mcw_cpf_custom_column', 10, 2);
	add_action( 'manage_pages_custom_column', 'mcw_cpf_custom_column', 10, 2);
	add_action( 'manage_media_custom_column', 'mcw_cpf_media_custom_column', 10, 2);
}
function mcw_cpf_custom_column ( $column_name, $id ) {
	global $mcw_cpf_handlers;
	$post_type = get_post_type( $id );
	if ( array_key_exists( $post_type, $mcw_cpf_handlers ) ) {
		$mcw_cpf_handlers[$post_type]->custom_column( $column_name, $id );
	}
}
function mcw_cpf_media_custom_column( $column_name, $id ) {
	global $mcw_cpf_handlers;
	$mcw_cpf_handlers['attachment']->custom_column( $column_name, $id );
}
?>