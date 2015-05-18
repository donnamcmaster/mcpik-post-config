<?php
/**
 *	Class mcw_PostFields_page
 *
 *	For the built-in post type, 'page'.
 *	Does NOT register the post type; it is a vehicle to extend it or customize display.
 *
 *	The easiest way to extend this post_type is to copy this file 
 *	into your child theme folder and modify it. 
 *
 *	@package McWebby_Base
 *	@subpackage Post_Type_Support
 *	@since McWebby Base 2.0
 */

Class mcw_PostFields_page extends mcw_PostFields {

function __construct ( ) {
	parent::init_post_fields( 'page' );
	$this->registered = true;
}


/**
 *	Display Methods
 */


/**
 *	Admin Methods
 */

public function manage_columns ( $defaults ) {
    unset( $defaults['date'] );
    unset( $defaults['comments'] );
    unset( $defaults['author'] );
    $defaults['slug'] = __( 'Slug' );
    return $defaults;
}

} // class mcw_PostFields_xxx
?>