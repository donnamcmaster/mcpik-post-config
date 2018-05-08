<?php
/**
 *	Class McPik_Post_Type_Attachment
 *
 *	For the built-in post type, 'attachment'.
 *	Does NOT register the post type; it is a vehicle to extend it or customize display.
 *
 *	@package McPik Post Types
 *	@since McPik Post Types 1.0
 */

Class McPik_Post_Type_Attachment extends McPik_Post_Type {

function __construct ( ) {
	parent::init_post_type( 'attachment' );

	remove_post_type_support( 'attachment', 'comments' );
	remove_post_type_support( 'attachment', 'custom-fields' );
}


public function manage_columns ( $defaults ) {
	unset( $defaults['comments'] );
	$defaults['title'] = __( 'File/Title' );
	$defaults['thumb'] = __( 'Thumbnail' );
	$defaults['img_size'] = __( 'Size' );
	$defaults['_wp_attachment_image_alt'] = __( 'Alt Text' );
	$defaults['post_excerpt'] = __( 'Caption' );
//	$defaults['author'] = __( 'Uploaded By' );
//	$defaults['post_content'] = __( 'Description' );
//	$defaults['date'] = __( 'Upload Date' );
	return $defaults;
}

} // class