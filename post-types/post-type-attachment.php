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
	$this->registered = true;
	parent::init_post_type( 'attachment' );

	remove_post_type_support( 'attachment', 'comments' );
	remove_post_type_support( 'attachment', 'custom-fields' );

	$this->post_fields['_wp_attachment_image_alt'] = array(
		'scope' => 'post_meta',
		'type' => 'text',
		'col_title' => 'Alt Text',
	);
	$this->post_fields['_mcw_photo_credit'] = array(
		'scope' => 'post_meta',
		'type' => 'select',
		'label' => 'Photographer to Credit',
		'col_title' => 'Photo Credit',
		'post_type' => 'photographer',
	);

	$this->meta_boxes = array(
		'credits' => array(
			'_mcw_photo_credit',
		),
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
	$defaults['_mcw_photo_credit'] = __( 'Photo Credit' );
//	$defaults['author'] = __( 'Uploaded By' );
//	$defaults['post_excerpt'] = __( 'Caption' );
//	$defaults['post_content'] = __( 'Description' );
//	$defaults['date'] = __( 'Upload Date' );
	return $defaults;
}

} // class