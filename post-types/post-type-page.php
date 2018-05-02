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
	$this->registered = true;
	parent::init_post_type( 'page' );

	remove_post_type_support( 'page', 'comments' );
	remove_post_type_support( 'page', 'custom-fields' );

	// NOTE: credits fields & metabox definition are shared by posts & pages
	$this->post_fields['byline'] = array(
		'scope' => 'post_meta',
		'type' => 'editor',
		'label' => 'Optional Credit Line',
		'description' => 'E.g., "Thanks to Scott Armstrong of All-Outdoors" where "All-Outdoors" is a link to their site.',
		'col_title' => 'Byline',
		'col_width' => 'medium',
		'options' => array (
			'media_buttons' => false,
			'teeny' => true,
		),
	);
	$this->post_fields['photo_credits'] = array(
		'scope' => 'post_meta',
		'type' => 'checkbox',
		'label' => 'Photographers to Credit',
		'description' => 'Featured images are handled automatically; no need to credit here.',
		'input_type' => 'post_list',
		'post_type' => 'photographer',
		'col_title' => 'Credits',
		'col_width' => 'medium',
		'choices' => piklist(
			get_posts( array(
				'post_type' => 'photographer',
				'numberposts' => -1,
				'orderby' => 'title',
				'order' => 'ASC'
			)),
			array( 'ID', 'post_title' )
		),
	);
	$this->meta_boxes = array(
		'credits' => array(
			'byline',
//			'photo_credits',
		),
	);
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
//    $defaults['photo_credits'] = __( 'Photos by' );
	$defaults['thumb'] = __( 'Thumbnail' );
    $defaults['slug'] = __( 'Slug' );
    $defaults['byline'] = __( 'Byline' );
    return $defaults;
}

} // class