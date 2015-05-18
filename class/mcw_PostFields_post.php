<?php
/**
 *	Class mcw_PostFields_post
 *
 *	For the built-in post type, 'post'.
 *	Does NOT register the post type; it is a vehicle to extend it or customize display.
 *
 *	The easiest way to extend this post_type is to copy this file 
 *	into your child theme folder and modify it. 
 *
 *	@package McWebby_Base
 *	@subpackage Post_Type_Support
 *	@since McWebby Base 2.0
 */

Class mcw_PostFields_post extends mcw_PostFields {

function __construct ( ) {
	parent::init_post_fields( 'post' );
	$this->registered = true;

	$this->default_taxonomy = 'category';

	$this->default_sort_by = 'post_date';
	$this->default_sort_order = 'DESC';

	$this->post_fields['thumb']['img_size'] = 'thumbnail';
}


/**
 *	Admin Methods
 */

public function manage_columns ( $defaults ) {
    $defaults['thumb'] = __( 'Thumbnail' );
    return $defaults;
}

} // class mcw_PostFields_xxx
?>