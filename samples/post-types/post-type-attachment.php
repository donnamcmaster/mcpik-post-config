<?php
/**
 *	Class McPik_Post_Type_attachment
 *
 *	For the built-in post type, 'attachment'.
 *	Does NOT register the post type; it is a vehicle to extend it or customize display.
 *
 *	@package McWebby Custom Posts
 *	@since McWebby Custom Posts 1.0
 */

Class McPik_Post_Type_attachment extends McPik_Post_Type_ {

function __construct ( ) {
	$this->registered = true;
	parent::init_post_type( 'attachment' );

	$this->post_fields['_wp_attachment_image_alt'] = array(
		'scope' => 'post_meta',
		'type' => 'text',
		'col_title' => 'Alt Text',
	);
}

function init_filters_and_actions() {
}


/**
 *	Display Methods
 */


/**
 *	Admin Methods
 */

public function manage_columns ( $defaults ) {
	unset( $defaults['comments'] );
	$defaults['title'] = __( 'File/Title' );
	$defaults['img_size'] = __( 'Size' );
	$defaults['thumb'] = __( 'Thumbnail' );
	$defaults['_wp_attachment_image_alt'] = __( 'Alt Text' );
//	$defaults['author'] = __( 'Uploaded By' );
//	$defaults['post_excerpt'] = __( 'Caption' );
//	$defaults['post_content'] = __( 'Description' );
//	$defaults['date'] = __( 'Upload Date' );
	return $defaults;
}

} // class