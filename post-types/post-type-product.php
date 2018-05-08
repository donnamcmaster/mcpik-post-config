<?php
/**
 *	Class McPik_Post_Type_product
 *
 *	Custom functions for WooCommerce products that represent shuttle routes. 
 *
 *	@package McPik Post Types
 *	@since McPik Post Types 1.0
 */

Class McPik_Post_Type_product extends McPik_Post_Type {

function __construct ( ) {
	parent::init_post_type( 'product' );

	$this->post_fields['cut_off_time'] = array(
		'scope' => 'post_meta',
		'type' => 'text',
	);
}


public function manage_columns ( $defaults ) {
	$defaults['cut_off_time'] = __( 'Cut-off' );
    return $defaults;
}

} // class
