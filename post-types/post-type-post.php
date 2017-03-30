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

	$this->post_fields['category'] = array(
		'scope' => 'taxonomy',
	);
	$this->post_fields['tags'] = array(
		'scope' => 'taxonomy',
	);
	$this->default_taxonomy = 'category';

	$this->default_sort_by = 'post_date';
	$this->default_sort_order = 'DESC';

	$this->post_fields['thumb']['img_size'] = 'thumbnail';

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
			'photo_credits',
		),
	);
}


/**
 *	Admin Methods
 */
public function manage_columns ( $defaults ) {
	$defaults['thumb'] = __( 'Thumbnail' );
	$defaults['byline'] = __( 'Byline' );
	$defaults['photo_credits'] = __( 'Photos by' );
	return $defaults;
}

} // class