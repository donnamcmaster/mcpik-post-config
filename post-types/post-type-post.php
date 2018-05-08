<?php
/**
 *	Class McPik_Post_Type_Post
 *
 *	For the built-in post type, 'post'.
 *	Does NOT register the post type; it is a vehicle to extend it or customize display.
 *
 *	The easiest way to extend this post_type is to copy this file 
 *	into your child theme folder and modify it. 
 *
 *	@package McPik Post Types
 *	@since McPik Post Types 1.0
 */

Class McPik_Post_Type_Post extends McPik_Post_Type {

function __construct ( ) {
	$this->registered = true;
	parent::init_post_type( 'post' );

//	remove_post_type_support( 'post', 'comments' );
	remove_post_type_support( 'post', 'custom-fields' );

	$this->post_fields['thumb']['img_size'] = 'thumbnail';

	// NOTE: byline & metabox definition are shared by posts & pages
	$this->post_fields['byline'] = array(
		'scope' => 'post_meta',
		'type' => 'editor',
	);
}


public function manage_columns ( $defaults ) {
	$defaults['thumb'] = __( 'Thumbnail' );
	$defaults['byline'] = __( 'Byline' );
	return $defaults;
}

} // class