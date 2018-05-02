<?php
/**
 *	Class McPik_Post_Type_sponsor
 *
 *	Define simple sponsor logos, names and URLs. 
 *	Developed for CPS Foundation; used as a basis for customized sponsor post types.
 *	
 *	@package McWebby
 *	@subpackage Post_Type_Support
 *	@since McWebby Base 2.0
 */

Class McPik_Post_Type_sponsor extends McPik_Post_Type {

function __construct ( ) {

	$this->register_args = array(
		'public' => true,
		'publicly_queryable' => false,
		'show_ui' => true, 
		'query_var' => false,
		'rewrite' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'exclude_from_search' => true,
		'menu_icon' => 'dashicons-heart',
		'supports' => array( 'title', 'thumbnail' ),
		'hide_meta_box' => array(
			'members-cp',
		),
	); 

	parent::init_post_type( 'sponsor' );

	$this->post_fields['_mcw_sponsor_url'] = array(
		'scope' => 'post_meta',
		'label' => 'URL to link to',
		'help' => 'Enter web address, beginning with http://',
		'type' => 'text',
		'col_title' => 'URL',
		'col_width' => 'wide',
		'attributes' => array(
			'placeholder' => 'http://',
			'class' => 'regular-text',
		),
	);
	$this->post_thumbnails = array(
		'sponsor_logo' => array (
			'width' => 100,
			'height' => 100,
			'crop' => false,
			'class' => 'sponsor_logo',
		),
	);
	$this->post_fields['thumb']['img_size'] = 'sponsor_logo';

	$this->meta_boxes = array(
		'details' => array(
			'_mcw_sponsor_url',
		),
	);
}

/**
 *	Display Methods
 */

function display_list () {
	mcw_start_ulist( '', 'sponsors' );
	$args = array (
		'post_type' => 'sponsor',
		'orderby' => 'rand',
		'numberposts' => 99,
		);
	$sponsors = get_posts( $args );
	foreach ( $sponsors as $sponsor ) {
		$thumb_id = get_post_meta( $sponsor->ID, '_thumbnail_id', true );
		if ( !$thumb_id ) {
			continue;
		}
		$url = get_post_meta( $sponsor->ID, '_mcw_sponsor_url', true );
		if ( $url ) {
			$prefix = '<a href="' . $url . '" title="' . $sponsor->post_title . '" target="_blank">';
			$suffix = '</a>';
		} else {
			$prefix = $suffix = '';
		}
		echo '<li>', $prefix, get_the_post_thumbnail( $sponsor->ID, 'sponsor_logo' ), $suffix, '</li>', PHP_EOL;
	}
	mcw_end_ulist( 'sponsors' );
}


/**
 *	Admin Methods
 */

public function manage_columns ( $defaults ) {
    unset( $defaults['comments'] );
    unset( $defaults['author'] );
    $defaults['_mcw_sponsor_url'] = __( 'URL' );
    $defaults['thumb'] = __( 'Image' );
    return $defaults;
}

} // class