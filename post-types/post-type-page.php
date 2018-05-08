<?php
/**
 *	Class McPik_Post_Type_Page
 *
 *	For the built-in post type, 'page'.
 *	Does NOT register the post type; it is a vehicle to extend it or customize display.
 *
 *	The easiest way to extend this post_type is to copy this file 
 *	into your child theme folder and modify it. 
 *
 *	@package McPik Post Types
 *	@since McPik Post Types 1.0
 */

Class McPik_Post_Type_Page extends McPik_Post_Type {

function __construct ( ) {
	parent::init_post_type( 'page' );

	remove_post_type_support( 'page', 'comments' );
	remove_post_type_support( 'page', 'custom-fields' );

	// NOTE: byline & metabox definition are shared by posts & pages
	$this->post_fields['byline'] = array(
		'scope' => 'post_meta',
		'type' => 'editor',
	);
}


public function manage_columns ( $defaults ) {
    unset( $defaults['date'] );
    unset( $defaults['comments'] );
    unset( $defaults['author'] );
	$defaults['thumb'] = __( 'Thumbnail' );
    $defaults['slug'] = __( 'Slug' );
    $defaults['byline'] = __( 'Byline' );
    return $defaults;
}

} // class