<?php
/**
 *	McWebby Custom Post Types: Configuration
 *
 *	This will be replaced with an interface later. 
 *
 *	@package McWebby Custom Post Types
 *	@subpackage Configuration
 *	@since McWebby Custom Post Types 0.9
 */

/**
 *	mcw_cpt_post_types and mcw_cpt_taxonomies
 *	- list the post types and taxonomies to be created
 */

$mcw_cpt_post_types = array (
	'vehicle',
	'route',
	'shuttle',
	'reservation',
	'passcard',
	'photographer',
);

$mcw_cpt_taxonomies = null;

/**
 *	$mcw_cpw_type_supports
 *	- some basic configuration of built-in post types
 */
$mcw_cpw_type_supports = array(
	'post' => array( 'index_headline', 'comments', 'post_nav', 'link_pages', 'excerpt' ),
	'page' => array( 'link_pages', 'excerpt' ),
	'default' => array( 'link_pages' ),
);

