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

	$this->post_thumbnails = array(
		'listing_img' => array(
			'width' => 100,
			'height' => 100,
			'crop' => false,
			'class' => 'listing_img',
		),
	);
	$this->post_fields['thumb']['img_size'] = 'listing_img';


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
	add_action( 'save_post', array( $this, 'write_directory_files' ) );
}


/**
 *	Display Methods
 */

/**
 *	Shortcode: dir_listings
 *	[dir_list cat="food-2"]
 *		include category in file food-2.inc, with no headline
 *	[dir_list cat="food-2" head="Local Restaurants"]
 *		include category in file food-2.inc, with h2 headline
 *	[dir_list] or [dir_list cat="*"] or [dir_list cat="directory"]
 *		include all categories, with jumplinks at the top and <h2> for each cat
 */
function dir_list( $atts ) {
	// initialize return value
	$s = '';
	extract( shortcode_atts( array(
		'cat' => '*',
		'head' => '',
	), $atts ) );

	// open local files with fopen()
	if ( $cat == '*' ) {
		$cat = 'directory';
	}
	$fname = INCLUDES_PATH . $cat . '.inc';
	if ( file_exists( $fname) ) {
		$handle = fopen( $fname, 'r' );
		$s = $head ? "<h2>$head</h2>" : '';
		$s .= fread( $handle, filesize( $fname ) );
		fclose( $handle );
	} else {
		mcw_log( "mcw_include: local file $fname not found" );
	}
	return $s;
}

function get_meta_item ( $item, $list ) {
	return array_key_exists( $item, $list ) ? $list[$item] : '';
}

function get_cat_listing ( $catobj ) {
	// get the listings
	$args = array (
		'post_type' => 'listing',
		'orderby' => $this->default_sort_by,
		'order' => $this->default_sort_order,
		'numberposts' => -1,
		'dir_cat' => $catobj->slug,
	);
	$listings = get_posts( $args );
	$s = '<ul class="dir_listings">' . PHP_EOL;

	foreach ( $listings as $listing ) {
		$meta = mcw_get_simple_post_custom( $listing->ID );
		$_mcw_listing_alt_title = $this->get_meta_item( '_mcw_listing_alt_title', $meta );
		$_mcw_listing_type = $this->get_meta_item( '_mcw_listing_type', $meta );
		$_mcw_listing_url = $this->get_meta_item( '_mcw_listing_url', $meta );
		$_mcw_listing_phone = $this->get_meta_item( '_mcw_listing_phone', $meta );
		$_mcw_listing_email = $this->get_meta_item( '_mcw_listing_email', $meta );

		$title = $_mcw_listing_alt_title ? $_mcw_listing_alt_title : $listing->post_title;

		// set defaults for basic, then pick up URL & descr for others
		$prefix = $suffix = '';
		if ( ( $_mcw_listing_type != 'bas' ) && $_mcw_listing_url ) {
			$prefix = '<a href="' . $_mcw_listing_url . '" target="_blank">';
			$suffix = '</a>';
		}
		$email = $_mcw_listing_email ? ' ' . mcw_get_mailto ( $_mcw_listing_email, $_mcw_listing_email ) : '';
		if ( $_mcw_listing_type != 'bas' ) {
			$descr = BR . apply_filters( 'the_content', $listing->post_content . $email . ' ' . $_mcw_listing_phone );
		} else {
			$descr = ' - ' . wptexturize( $listing->post_content ) . $email . ' ' . $_mcw_listing_phone;
		}

		if ( $_mcw_listing_type == 'prem' ) {
			$image = get_the_post_thumbnail( $listing->ID, 'listing_img' );
			$s .= '<li class="prem_listing cf"><div class="list_img">' . $image .'</div><div class="list_body"><strong>' . $prefix . $title . $suffix . '</strong> ';
		} else {
			$s .= '<li><div class="list_body"><strong>' . $prefix . $title . $suffix . '</strong>';
		}
		$s .= $descr . '</div></li>' . PHP_EOL;
	}
	$s .= '</ul>' . PHP_EOL;
	return $s;
}


/**
 *	Write Directory Files:
 *	called whenever a post is saved (hook save_post)
 *	if this is an updated listing, then we write a new set of files to INCLUDES_PATH
 *	files written include "directory.inc" (all listings, jump links, headers) plus
 *		one listings-only file for each cat, named "<cat_name>.inc"
 *	future optimization: write only the file(s) for the cat(s) containing this listing
 *	note that in spite of the opt, we would still need to parse all listings/cats
 *		in order to create the full directory file
 *	creates a lock file for the duration of the writes to prevent conflict
 *	to avoid browsers picking up partial listings, new files are written as "<fname>.tmp"; 
 *		once a file is complete, the "<fname>.inc" is deleted and the tmp file is renamed 
 */

function write_directory_files ( $post_id ) {
	// if it's a revision or the wrong post type, we're not interested
	if ( wp_is_post_revision( $post_id ) || ( $this->post_type != get_post_type( $post_id ) ) ) {
        return;
    }
    
    // use lock file to prevent access conflict
    if ( file_exists( INCLUDES_LOCK_FILE ) ) {
    	mcw_log( "write_directory_files: access conflict" );
    	return;
    }
    $lock_handle = fopen( INCLUDES_LOCK_FILE, 'w' ) or die( 'Cannot open file:  '.INCLUDES_LOCK_FILE );
    fclose( $lock_handle );

	// open a temporary file for the entire directory
	$temp_dir_fname = INCLUDES_PATH . 'directory.tmp';
	$dir_fname = INCLUDES_PATH . 'directory.inc';
    if ( file_exists( $temp_dir_fname ) ) {
		unlink( $temp_dir_fname );
	}
    $dir_handle = fopen( $temp_dir_fname, 'w' ) or die( 'Cannot open file:  '.$temp_dir_fname );

    $cats_list = get_terms( 'dir_cat' );

	// jump links for full directory listing
	$nr_cats = count( $cats_list );
	$nr_cols = 2;
	$nr_rows = $nr_cats / $nr_cols;
	if ( ( $nr_cats % $nr_cols) <> 0 ) {
		$nr_rows = ceil( $nr_rows );
	}
	settype( $nr_rows,'int' );

	// print jumplinks
	$s = '<div id="jump_links" class="cf">' . PHP_EOL . '<ul id="jump_1" class="jump-links">';
	$cat_count = 1;
	$col = 1;
	foreach ( $cats_list as $catobj ) {
		if ( ( $col == 1 ) && ( $cat_count > $nr_rows ) ) {
			$s .=  '</ul>' . PHP_EOL . '<ul id="jump_2" class="jump-links">';
			$col++;
		}
		$s .= PHP_EOL . '<li>' . mcw_get_anchor( '#dircat_'.$catobj->term_id, $catobj->name ) . '</li>';
		$cat_count++;
	}
	$s .=  '</ul></div>' . PHP_EOL;
	fwrite( $dir_handle, $s );

	// each cat is written to the directory and to an individual file
	reset( $cats_list );
	foreach ( $cats_list as $catobj ) {
		$this->write_cat_to_files( $catobj, $dir_handle );
	}
	
	// close temp directory file; delete old .inc file and rename .tmp to .inc
    fclose( $dir_handle );
    if ( file_exists( $dir_fname ) ) {
		unlink( $dir_fname );
	}
	rename( $temp_dir_fname, $dir_fname);

	// must remove the lock file!
	unlink( INCLUDES_LOCK_FILE );
}

function write_cat_to_files ( $catobj, $dir_handle ) {
	$tempname = INCLUDES_PATH . $catobj->slug . '.tmp';
	$fname = INCLUDES_PATH . $catobj->slug . '.inc';
    if ( file_exists( $tempname ) ) {
		unlink( $tempname );
	}
    $cat_handle = fopen( $tempname, 'w' ) or die( 'Cannot open file:  '.$tempname );

	fwrite( $dir_handle, PHP_EOL . '<h2 id="dircat_' . $catobj->term_id . '">' . $catobj->name . '</h2>' . PHP_EOL );
	$cat_string = $this->get_cat_listing( $catobj );
	fwrite( $dir_handle, $cat_string );
	fwrite( $cat_handle, $cat_string );

	// close the temp file; delete the previous file; rename temp
    fclose( $cat_handle );
    if ( file_exists( $fname ) ) {
		unlink( $fname );
	}
	rename( $tempname, $fname);
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