<?php
/**
 *	Class McPik_Post_Type_Photographer
 *
 *	For crediting photographers. The attachments post_type can link to a photographer. 
 *	Includes title, description, URL and thumbnail. 
 *
 *	@package McPik Post Types
 *	@since McPik Post Types 1.0
 */

Class McPik_Post_Type_Photographer extends McPik_Post_Type {

function __construct ( ) {

	$this->register_args = array(
		'public' => false,
		'publicly_queryable' => false,
		'show_ui' => true, 
		'query_var' => false,
		'rewrite' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_icon' => 'dashicons-camera',
		'supports' => array( 'title', 'thumbnail', 'editor' ),
		'hide_meta_box' => array(
			'slug',
			'members-cp',
		),
	); 

	parent::init_post_type( 'photographer' );

	$this->default_sort_by = 'title';
	$this->default_sort_order = 'ASC';

	// for displaying photography credits at the bottom of the page
	$this->credit_count = 0;
	$this->credits = array();

	$this->post_thumbnails = array(
		'listing_img' => array(
			'width' => 225,
			'height' => 225,
			'crop' => false,
			'class' => 'photog_img',
		),
	);
	$this->post_fields['thumb']['img_size'] = 'photog_img';

	// define additional post fields
	$this->url_field = '_mcw_photog_url';
	$this->post_fields['_mcw_photog_url'] = array(
		'type' => 'text',
		'scope' => 'post_meta',
		'label' => "Photographer's website",
		'description' => 'Enter web address, beginning with http://',
		'col_title' => 'URL',
		'attributes' => array(
			'placeholder' => 'http://',
			'class' => 'regular-text',
		),

	);

	$this->meta_boxes = array(
		'details' => array(
			'_mcw_photog_url',
		),
	);
}

protected function init_filters_and_actions () {
	add_shortcode( 'mcw_photographers', array( $this, 'mcw_photographers' ) );
}


/**
 *	Display Methods
 */

function add_credit ( $image_id ) {
	$photog_id = get_post_meta( $image_id, '_mcw_photo_credit', true);
	if ( $photog_id && !in_array( $photog_id, $this->credits ) ) { 
		$this->credits[] = $photog_id; 
		$this->credit_count ++;
	} 
}

function get_credits () {
	if ( !$this->credits ) {
		return '';
	}

	$photo_credit = $this->get_linked_name( $this->credits[0] ); 
	if ( $this->credit_count > 1 ) {
		for ( $credit=1; $credit < $this->credit_count-1; $credit++ ) {
			$photo_credit .=  ', ' . $this->get_linked_name( $this->credits[$credit] );
		}
		$photo_credit .= ' &amp; ' . $this->get_linked_name( $this->credits[$this->credit_count-1] );
	}
	return $photo_credit;
}


/**
 *	photographers shortcode
 *	[mcw_photogs]	list all photographers
 *	[mcw_photogs link=1] list all; each one links to a page with more details
 */
public function mcw_photographers ( $atts ) {
	extract( shortcode_atts( array(
		'link' => 0,
	), $atts ) );

	// initialize query
	$args = array (
		'post_type' => 'photographer',
		'orderby' => 'name',
		'order' => 'ASC',
		'posts_per_page' => 99,
	);

	// start loop
	$photographers = get_posts( $args );

	$s = '<ul id="photographer_list" class="plain-list">' . PHP_EOL;
	foreach ( $photographers as $photog ) {
//		$content = apply_filters( 'the_content', $photog->post_content );
//		$image = get_the_post_thumbnail( $photog->ID, 'photog_img' );
		$image = '';
		$s .= '<li>' . $image .'<p>' . $this->get_linked_name( $photog->ID, $photog->post_title );
		$s .= get_edit_post_link( 'edit photographer', '', $photog->ID ) . '<br>';
		$s .= '</p></li>' . PHP_EOL;
	}
	$s .= '</ul>' . PHP_EOL;
	return $s;
}


/**
 *	Admin Methods
 */

public function manage_columns ( $defaults ) {
	$defaults['thumb'] = __( 'Image' );
	$defaults['_mcw_photog_url'] = __( 'URL' );
	$defaults['post_content'] = __( 'Description' );
	return $defaults;
}

} // class
?>