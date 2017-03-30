<?php
/**
 *	Class McPik_Post_Type_Ad
 *
 *	Define simple banner ad for Coloma.com and TheAmericanRiver.com. 
 *	For each page (except the home page), the code selects a number of ads
 *	in random order. 
 *	
 *	The following should be defined in the functions.php file: 
 *	COLO_MAX_ADS - maximum number of ads to display on a page
 *	COLO_MAX_AD_HEIGHT - total vertical space allowed for ads (in pixels)
 *	Defaults for each are defined below. 
 *
 *	@package McPik Post Types
 *	@since McPik Post Types 1.0
 */

if ( !defined( 'COLO_AD_COLS' ) ) {
	define( 'COLO_AD_COLS', 4 );
}

Class McPik_Post_Type_Ad extends McPik_Post_Type {

function __construct ( ) {

	$this->register_args = array(
		'public' => false,
		'publicly_queryable' => false,
		'show_ui' => true, 
		'query_var' => false,
		'rewrite' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'exclude_from_search' => true,
		'menu_icon' => 'dashicons-megaphone',
		'supports' => array( 'title', 'thumbnail' ),
		'hide_meta_box' => array(
			'members-cp',
		),
	); 

	parent::init_post_type( 'ad' );

	// post_parent is used for the page the ad is fixed to (if applicable)
	$default_page = array( 0 => '-- none (rotate between pages) --' );
	$choices = $default_page + piklist(
		get_posts( array(
			'post_type' => 'page',
			'numberposts' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		)),
		array( 'ID', 'post_title' )
	);
//piklist::pre($choices);
	$this->post_fields['post_parent'] = array(
		'scope' => 'post',
		'type' => 'select',
		'value' => 0,
		'input_type' => 'single_post',
		'label' => 'Page this ad is displayed on',
		'description' => 'Leave blank for rotating ads',
		'post_type' => 'page',
		'db_field' => 'post_parent',
		'col_title' => 'Page',
		'col_width' => 'medium',
		'choices' => $choices,
	);

	$this->post_fields['_mcw_ad_business'] = array(
		'scope' => 'post_meta',
		'type' => 'select',
		'input_type' => 'single_post',
		'label' => 'Business purchasing this ad',
		'post_type' => 'business',
		'col_title' => 'Business',
		'col_width' => 'wide',
		'choices' => piklist(
			get_posts( array(
				'post_type' => 'business',
				'numberposts' => -1,
				'orderby' => 'title',
				'order' => 'ASC'
			)),
			array( 'ID', 'post_title' )
		)
	);
	$this->post_fields['_mcw_ad_url'] = array(
		'scope' => 'post_meta',
		'label' => 'URL to link to',
		'description' => 'Enter web address, beginning with http://',
		'type' => 'text',
		'col_title' => 'URL',
		'col_width' => 'wide',
		'attributes' => array(
			'class' => 'regular-text',
		),
	);
	$this->post_fields['_mcw_ad_height'] = array(
		'scope' => 'post_meta',
		'label' => 'Ad height (pixels)',
		'description' => 'All ads are 150px wide. Supported height values are 60, 90, 120, 150, 240, or 300.',
		'type' => 'select',
		'choices'	=>	array(
			60 => 60, 
			90 => 90, 
			120 => 120, 
			150 => 150, 
			240 => 240, 
			300 => 300
		),
		'null_ok'	=>	false,
		'col_title' => 'Height',
		'col_width' => 'narrow',
	);

	$this->post_thumbnails = array(
		'ad_img' => array (
			'width' => 150,
			'height' => 300,
			'crop' => false,
			'class' => 'ad_img',
		),
	);
	$this->post_fields['thumb']['img_size'] = 'ad_img';

	$this->post_fields['menu_order'] = array(
		'scope' => 'post',
		'type' => 'display-only',
		'label' => 'Counter (number of displays since last reset)',
		'input_type' => 'display-only',
		'col_title' => 'Counter',
		'col_align' => 'right',
		'col_width' => 'narrow',
	);
	$this->post_fields['_mcw_ad_odo'] = array(
		'scope' => 'post_meta',
		'type' => 'display-only',
		'label' => 'Odometer (total number of times ad has been displayed)',
		'input_type' => 'display_only',
		'col_title' => 'Odometer',
		'col_align' => 'right',
		'col_width' => 'narrow',
	);
	
	$this->meta_boxes = array(
		'details' => array(
			'_mcw_ad_business',
			'_mcw_ad_url',
			'_mcw_ad_height',
		),
		'fixed' => array(
			'post_parent',
		),
		'counters' => array(
			'menu_order',
			'_mcw_ad_odo',
		),
	);
}

protected function init_filters_and_actions () {
	add_action( 'wp_insert_post', array( $this, 'auto_reset_counters' ) );
}

public function auto_reset_counters ( $post_id ) {
	global $wpdb;

	// first check to make sure it's an ad
	if ( get_post_type( $post_id ) !== 'ad' ) {
		return;
	}

	// then check for an existing odometer, which would indicate the ad is not new
	if ( $wpdb->get_var( "
		SELECT COUNT(*) FROM $wpdb->postmeta
		WHERE (post_id=$post_id) AND (meta_key='_mcw_ad_odo')" ) ) {
		return;
	}

	// we get here if it's a new ad; best to use wpdb as wp treats meta as strings not int
	$wpdb->query(
		$wpdb->prepare( 
			"
			INSERT INTO $wpdb->postmeta
			( post_id, meta_key, meta_value )
			VALUES ( %d, %s, %d )
			", 
			$post_id, 
			'_mcw_ad_odo', 
			0 
		)
	);

	// and reset all trip counters
	$wpdb->query(
		"
		UPDATE $wpdb->posts 
		SET menu_order = 0
		WHERE post_type = 'ad' 
		"
	);
}

public function update_counters ( $post_id ) {
	global $wpdb;
	$wpdb->query(
		"
		UPDATE $wpdb->posts 
		SET menu_order = menu_order+1
		WHERE ID = $post_id
		"
	);
	$wpdb->query(
		"
		UPDATE $wpdb->postmeta 
		SET meta_value = meta_value+1
		WHERE (post_ID = $post_id) AND (meta_key = '_mcw_ad_odo')
		"
	);
}

/**
 *	Display Methods
 */
function display_ad_panel ( $posts=null ) {
	global $post;

	// first check for ads fixed to this page
	$args = array (
		'post_type' => 'ad',
		'orderby' => 'rand',
		'numberposts' => COLO_AD_COLS,
		'post_parent' => $post->ID,
		);
	$static_ads = get_posts( $args );

	// are slots left for rotating ads?
	// get twice as many ads as slots to account for duplicate businesses
	$nr_rotators = COLO_AD_COLS - count( $static_ads );
	if ( $nr_rotators ) {
		$args = array (
			'post_type' => 'ad',
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'post_parent' => 0,
			'numberposts' => $nr_rotators * 2,
			);
		$rotators = get_posts( $args );
	}

	$this->businesses = array();
	$this->cols_printed = 0;

?>
<hr class="banner-border above">
<ul class="banner-ads row">

<?php
	if ( $static_ads ) {
		$this->print_ad_list( $static_ads );
	}
	if ( $rotators ) {
		$this->print_ad_list( $rotators );
	}
?>
</ul><!-- banner-ads row -->
<hr class="banner-border below">

<?php
}

private function print_ad_list ( $ads ) {
	foreach ( $ads as $ad ) {
		if ( $this->cols_printed >= COLO_AD_COLS ) {
			break;
		}
		extract( mcw_get_simple_post_custom( $ad->ID ) );
		if ( !isset( $_thumbnail_id ) || !$_thumbnail_id || in_array( $_mcw_ad_business, $this->businesses ) ) {
			continue;
		}
?>
	<li class="col-sm-3 col-xs-6">
		<a href="<?= $_mcw_ad_url ?>" title="<?= $ad->post_title ?>" target="_blank">
		<?= get_the_post_thumbnail( $ad->ID, 'ad_img' ); ?>
		</a>
	</li>
<?php
		$this->update_counters( $ad->ID );
		$this->businesses[] = $_mcw_ad_business;
		$this->cols_printed += 1; // !!! will need to check for multi-col ads
	}
}


/**
 *	Admin Methods
 */

public function manage_columns ( $defaults ) {
	unset( $defaults['comments'] );
	unset( $defaults['author'] );
	$defaults['_mcw_ad_business'] = __( 'Business' );
	$defaults['post_parent'] = __( 'Fixed Page' );
	$defaults['_mcw_ad_url'] = __( 'URL' );
	$defaults['_mcw_ad_height'] = __( 'Height (px)' );
	$defaults['thumb'] = __( 'Image' );
	$defaults['menu_order'] = __( 'Counter' );
	$defaults['_mcw_ad_odo'] = __( 'Odometer' );
	return $defaults;
}

} // class
?>