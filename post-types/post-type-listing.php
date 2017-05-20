<?php
/**
 *	Class McPik_Post_Type_Listing
 *
 *	For Coloma.com and TheAmericanRiver.com
 *	Define simple business directory listing. 
 *
 *	@package McPik Post Types
 *	@since McPik Post Types 1.0
 */

Class McPik_Post_Type_Listing extends McPik_Post_Type {

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
		'menu_icon' => 'dashicons-index-card',
		'supports' => array( 'title', 'editor', 'revisions', 'thumbnail', 'page-attributes' ),

		// additional Piklist arguments
		'title' => 'Business Name for Sort Order',
		'edit_columns' => array(
			'title' => 'Name',
		),
		'hide_meta_box' => array(
			'revisions',
			'slug',
			'members-cp',
		),
	); 

	parent::init_post_type( 'listing' );

	$this->default_sort_by = 'menu_order title';
	$this->default_sort_order = 'ASC';
		
	// define & register taxonomy for this post_type
	$this->default_taxonomy = 'dir_cat';
	$tax_args = array(
		'hierarchical' => true,
		'label' => 'Directory Cat',
		'query_var' => true,
		'rewrite' => false
	);
	register_taxonomy( 'dir_cat', 'listing', $tax_args );
	$this->post_fields['dir_cat'] = array(
		'scope' => 'taxonomy',
		'col_title' => 'Category',
	);
	
	$this->post_fields['_mcw_listing_alt_title'] = array(
		'type' => 'text',
		'scope' => 'post_meta',
		'label' => 'Name to display (if different from title)',
		'description' => 'The title is used for sorting.',
		'col_title' => 'Alt Title',
		'attributes' => array(
			'class' => 'regular-text',
		),
	);
	$this->post_fields['_mcw_listing_type'] = array(
		'scope' => 'post_meta',
		'label' => 'Listing type',
		'col_title' => 'Type',
		'type' => 'radio',
		'choices'	=>	array(
			'bas' => 'Basic',
			'std' => 'Standard',
			'prem' => 'Premium',
		),
		'null_ok'	=>	false,
	);
	$this->post_fields['_mcw_listing_url'] = array(
		'type' => 'text',
		'scope' => 'post_meta',
		'label' => 'Website address',
		'description' => 'Enter web address, beginning with http://',
		'col_title' => 'URL',
		'attributes' => array(
			'class' => 'regular-text',
		),
	);
	$this->post_fields['_mcw_listing_email'] = array(
		'type' => 'text',
		'scope' => 'post_meta',
		'label' => 'Email address',
		'description' => 'If no website, you may enter email address here instead.',
		'col_title' => 'Email (if no URL)',
		'attributes' => array(
			'class' => 'regular-text',
		),
	);
	$this->post_fields['_mcw_listing_phone'] = array(
		'scope' => 'post_meta',
		'label' => 'Phone number',
		'col_title' => 'Phone',
		'type' => 'text',
	);

	$this->post_fields['thumb']['img_size'] = 'listing-img';

	$this->meta_boxes = array(
		'details' => array(
			'_mcw_listing_alt_title',
			'_mcw_listing_type',
			'_mcw_listing_url',
			'_mcw_listing_email',
			'_mcw_listing_phone',
		),
	);
}

protected function init_filters_and_actions () {
	add_shortcode( 'dir_list', array( $this, 'dir_list' ) );
}


/**
 *	Display Methods
 */

/**
 *	Shortcode: dir_listings
 *	[dir_list cat="food-2"]
 *		include category with slug food-2, with no headline
 *	[dir_list cat="food-2" head="Local Restaurants"]
 *		include category with slug food-2, with h2 headline
 *	[dir_list] or [dir_list cat="*"] or [dir_list cat="directory"]
 *		include all categories, with jumplinks at the top and <h2> for each cat
 */
public function dir_list( $atts ) {
	global $mcpk_pt_handlers;

	// pick up parameters
	extract( shortcode_atts( array(
		'cat' => '*',
		'head' => false,
		'ads' => true,
	), $atts ) );
	if ( $head == "true" ) {
		$head = true;
	}

	// get all categories? 
	if ( ( $cat == '*' ) || ( $cat == 'directory' ) ) {
		// pull requested info into output buffer
		ob_start();

	    $cats_list = get_terms( 'dir_cat' );
		echo $this->get_jump_links( $cats_list );

		if ( $ads ) {
			echo $mcpk_pt_handlers['ad']->display_ad_panel();
		}

		reset( $cats_list );
		$ad_break = ceil( count( $cats_list ) / 3 );
		$cat_num = 1;
		foreach ( $cats_list as $catobj ) {
			echo $this->get_cat_listing( $catobj, true );
			echo $this->get_back_to_top();
			if ( $ads && ( $cat_num++ == $ad_break ) ) {
				echo $mcpk_pt_handlers['ad']->display_ad_panel();
				$cat_num = 1;
			}
		}
		return ob_get_clean();

	} else {
		$catobj = get_term_by( 'slug', $cat, 'dir_cat' );
		if ( $catobj ) { 
			return $this->get_cat_listing( $catobj, $head );
		} else {
			mcw_log( "McPik_Post_Type_Listing::dir_list > can't find $cat" );
			return '';
		}
	}
}

private function get_meta_item ( $item, $list ) {
	return array_key_exists( $item, $list ) ? $list[$item] : '';
}

private function get_listing ( $listing ) {
	$meta = McPik_Utils::get_simple_post_custom( $listing->ID );
	$_mcw_listing_alt_title = $this->get_meta_item( '_mcw_listing_alt_title', $meta );
	$_mcw_listing_type = $this->get_meta_item( '_mcw_listing_type', $meta );
	$_mcw_listing_url = $this->get_meta_item( '_mcw_listing_url', $meta );
	$_mcw_listing_phone = $this->get_meta_item( '_mcw_listing_phone', $meta );
	$_mcw_listing_email = $this->get_meta_item( '_mcw_listing_email', $meta );

	$title = $_mcw_listing_alt_title ? wptexturize( $_mcw_listing_alt_title ) : $listing->post_title;

	// set defaults for basic, then pick up URL & descr for others
	$prefix = $suffix = '';
	if ( ( $_mcw_listing_type != 'bas' ) && $_mcw_listing_url ) {
		$prefix = '<a href="' . $_mcw_listing_url . '" target="_blank">';
		$suffix = '</a>';
	}

	// create anchor for email if configured
	$email = $_mcw_listing_email ? ' ' . McPik_Utils::get_anchor( "mailto:$_mcw_listing_email", $_mcw_listing_email ) : '';

	// basic does not display description
	if ( $_mcw_listing_type != 'bas' ) {
		$descr = BR . wptexturize( $listing->post_content ) . $email . ' ' . $_mcw_listing_phone;
	} else {
		$descr = ' - ' . $email . ' ' . $_mcw_listing_phone;
	}

	// premium: set class & add thumbnail if configured 
	$class = '';
	$image_code = '';
	if ( $_mcw_listing_type == 'prem' ) {
		$class = ' class="prem_listing cf"';
		$image = get_the_post_thumbnail( $listing->ID, 'listing-img' );
		$image_code = $image ? '<div class="list_img">' . $image .'</div>' : '';
	}

	// output listing
	ob_start();
?>
	<li<?= $class; ?>><?= $image_code; ?>
		<div class="list_body">
			<strong><?= $prefix, $title, $suffix; ?></strong>
			<?= $descr; ?>
		</div>
	</li>

<?php
	return ob_get_clean();
}


/*
	$heading:
		false or null value => don't display any heading
		boolean true => display cat name
		any other value => display that value
*/
private function get_cat_listing ( $catobj, $heading=false ) {

	ob_start();

	if ( $heading === true ) {
		$heading = $catobj->name;
	}
	if ( $heading ) {
?>
	<h2 class="jump-target" id="dircat_<?= $catobj->term_id; ?>"><?= $heading; ?></h2>

<?php
	}

?>
	<ul class="dir_listings">

<?php
	// get the premium listings with priority 0
	$args = array (
		'post_type' => 'listing',
		'menu_order' => 0,
		'orderby' => 'rand',
		'numberposts' => -1,
		'dir_cat' => $catobj->slug,
	);
	$listings = get_posts( $args );
	foreach ( $listings as $listing ) {
		echo $this->get_listing( $listing );
	}

	// get the remaining listings
	$args = array (
		'post_type' => 'listing',
		'orderby' => $this->default_sort_by,
		'order' => $this->default_sort_order,
		'numberposts' => -1,
		'dir_cat' => $catobj->slug,
	);
	$listings = get_posts( $args );
	foreach ( $listings as $listing ) {
		if ( $listing->menu_order > 0 ) { 
			echo $this->get_listing( $listing );
		}
	}
?>
	</ul>

<?php
	return ob_get_clean();
}


private function get_jump_links ( $cats_list ) {

	// jump links for full directory listing
	$nr_cats = count( $cats_list );
	$nr_cols = 3;
	$nr_rows = $nr_cats / $nr_cols;

	$mod = $nr_cats % $nr_cols;

	if ( ( $nr_cats % $nr_cols) > 0 ) {
		$nr_rows = ceil( $nr_rows );
	}
	settype( $nr_rows, 'int' );

	ob_start();
?>
	<div class="jump-links row">
		<ul class="col-sm-4">
<?php
	$row_count = 0;
	foreach ( $cats_list as $catobj ) {
		if ( ( $row_count >= $nr_rows ) ) {
?>
		</ul>
		<ul class="col-sm-4">
<?php
			$row_count = 0;
		}
?>
			<li><?= McPik_Utils::get_anchor( '#dircat_'.$catobj->term_id, $catobj->name ); ?></li>
<?php
		$row_count++;
	}
?>
		</ul>
	</div>
<?php
	
	return ob_get_clean();
}

private function get_back_to_top () {
	ob_start();
?>
<div class="clearfix"><a href="#top" class="btn btn-default back-to-top pull-right">
  <span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span> back to top
</a></div>

<?php
	return ob_get_clean();
}


/**
 *	Admin Methods
 */

public function manage_columns ( $defaults ) {
    unset( $defaults['comments'] );
    unset( $defaults['author'] );
    $defaults['_mcw_listing_alt_title'] = __( 'Sort By' );
    $defaults['dir_cat'] = __( 'Categories' );
    $defaults['_mcw_listing_type'] = __( 'Type' );
    $defaults['menu_order'] = __( 'Prem Order' );
    $defaults['thumb'] = __( 'Image' );
    return $defaults;
}

} // class
?>