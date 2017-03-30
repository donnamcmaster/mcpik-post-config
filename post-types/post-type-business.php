<?php
/**
 *	Class McPik_Post_Type_Business
 *
 *	Define business name; used to associate banner ads. 
 *
 *	@package McPik Post Types
 *	@since McPik Post Types 1.0
 */

Class McPik_Post_Type_Business extends McPik_Post_Type {

function __construct ( ) {

	$this->register_args = array(
		'public' => false,
		'show_ui' => true, 
		'query_var' => false,
		'rewrite' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'exclude_from_search' => true,
		'supports' => array( 'title' ),
		'menu_icon' => 'dashicons-store',
		
		// additional Piklist arguments
		'title' => 'Business Name',
		'edit_columns' => array(
			'title' => 'Name',
		),
		'hide_meta_box' => array(
			'slug',
			'members-cp',
		),
	); 
	parent::init_post_type( 'business', 'business', 'Business' );
}


/**
 *	Display Methods
 */


/**
 *	Admin Methods
 */

public function manage_columns ( $defaults ) {
    unset( $defaults['author'] );
    unset( $defaults['comments'] );
	unset( $defaults['date'] );
	return $defaults;
}

} // class
?>