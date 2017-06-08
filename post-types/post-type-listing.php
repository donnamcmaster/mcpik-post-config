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

define( 'INCLUDES_DIRECTORY', '/mcw-listing/' );
define( 'INCLUDES_PATH', $_SERVER['DOCUMENT_ROOT'] . INCLUDES_DIRECTORY );
define( 'INCLUDES_LOCK_FILE', INCLUDES_PATH . 'lock.txt' );

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

	// only coloma.com (& coloma.dev) config listings & write listings files; 
	// theamericanriver.com (& tar.dev) just read the files
	$this->is_coloma = ( strpos( $_SERVER['HTTP_HOST'], 'col' ) !== false );
	
	if ( !$this->is_coloma ) {
		// don't register the post_type
		$this->registered = true;
	}
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
	if ( $this->is_coloma ) {
		add_action( 'save_post', array( $this, 'write_directory_files' ) );
	}
}


/**
 *	Shortcode Display
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

		echo $this->retrieve_jump_links();

		if ( $ads ) {
			echo $mcpk_pt_handlers['ad']->display_ad_panel();
		}

	    $catlist = $this->retrieve_catlist();
		$cat_num = 1;
		$ad_break = ceil( sizeof( $catlist ) / 3 );
		foreach ( $catlist as $cat_info ) {
			echo $this->retrieve_cat_listing( $cat_info, true );
			echo $this->get_back_to_top();
			if ( $ads && ( $cat_num++ == $ad_break ) ) {
				echo $mcpk_pt_handlers['ad']->display_ad_panel();
				$cat_num = 1;
			}
		}
		return ob_get_clean();

	} else {
		$cat_info = get_term_by( 'slug', $cat, 'dir_cat', ARRAY_A );
		if ( $cat_info ) { 
			return $this->retrieve_cat_listing( $cat_info, $head );
		} else {
			mcw_log( "McPik_Post_Type_Listing::dir_list > can't find $cat" );
			return '';
		}
	}
}

/**
 *
 *	Write Listing Files
 *
 */

// pass the name without the ".xxx" extension
// returns a file handle
private function prepare_to_write_file ( $name ) {
	// open a temporary file
	$temp_fname = INCLUDES_PATH . "$name.tmp";
    if ( file_exists( $temp_fname ) ) {
		unlink( $temp_fname );
	}
    $file_handle = fopen( $temp_fname, 'w' ) or die( 'Cannot open file:  '.$temp_fname );
    return $file_handle;
}

private function save_and_close_file ( $file_handle, $name, $extension ) {
	$temp_fname = INCLUDES_PATH . "$name.tmp";
	$real_fname = INCLUDES_PATH . "$name.$extension";
    fclose( $file_handle );
    if ( file_exists( $real_fname ) ) {
		unlink( $real_fname );
	}
	rename( $temp_fname, $real_fname);
}

/**
 *	Write Directory Files: NEEDS UPDATE
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
	if ( !$this->is_coloma || wp_is_post_revision( $post_id ) || 
		( $this->post_type != get_post_type( $post_id ) ) ||
		( get_post_status( $post_id ) != 'publish' ) 
		) {
        return;
    }

	// to optimize and only rewrite affected category (ies)
//	$listing_cats = wp_get_post_terms( $post_id, 'dir_cat' );

    // use lock file to prevent access conflict
    if ( file_exists( INCLUDES_LOCK_FILE ) ) {
    	mcw_print_debug( "write_directory_files: access conflict" );
    	return;
    }
    $lock_handle = fopen( INCLUDES_LOCK_FILE, 'w' ) or die( 'Cannot open file:  '.INCLUDES_LOCK_FILE );
    fclose( $lock_handle );

	// pick up a list of categories
    $all_cats = get_terms( 'dir_cat' );

	// write a file for the category list 
    $catlist_handle = $this->prepare_to_write_file( 'catlist', 'json' );
	fwrite( $catlist_handle, json_encode( $all_cats ) );
	$this->save_and_close_file ( $catlist_handle, 'catlist', 'json' );

	// write a file for the jumplinks 
    $jumps_handle = $this->prepare_to_write_file( 'jumplinks', 'html' );
	fwrite( $jumps_handle, $this->get_jump_links( $all_cats ) );
	$this->save_and_close_file( $jumps_handle, 'jumplinks', 'html' );

	// each cat is written to an individual file
	reset( $all_cats );
	foreach ( $all_cats as $catobj ) {
		$this->write_cat_listing( $catobj );
	}
	
	// must remove the lock file!
	unlink( INCLUDES_LOCK_FILE );
}

private function write_cat_listing ( $catobj ) {

	// write the premium listings with priority 0 to a sortable array
	// & write the array to a json file
	$premium_0 = array();
	$args = array (
		'post_type' => 'listing',
		'menu_order' => 0,
		'numberposts' => -1,
		'dir_cat' => $catobj->slug,
	);
	$listings_0 = get_posts( $args );
	if ( $listings_0 ) {
		$premium_handle = $this->prepare_to_write_file ( $catobj->slug.'_0' );
		foreach ( $listings_0 as $listing ) {
			$premium_0[] = $this->write_single_listing( $listing );
		}
		fwrite( $premium_handle, json_encode( $premium_0 ) );
		$this->save_and_close_file( $premium_handle, $catobj->slug.'_0', 'json' );
	}

	// get the remaining listings
	// & write them to a text file
	$args = array (
		'post_type' => 'listing',
		'orderby' => $this->default_sort_by,
		'order' => $this->default_sort_order,
		'numberposts' => -1,
		'dir_cat' => $catobj->slug,
	);
	$listings = get_posts( $args );
	if ( $listings ) {
		$file_handle = $this->prepare_to_write_file ( $catobj->slug );
		foreach ( $listings as $listing ) {
			if ( $listing->menu_order > 0 ) { 
				fwrite( $file_handle, $this->write_single_listing( $listing ) );
			}
		}
		$this->save_and_close_file( $file_handle, $catobj->slug, 'html' );
	}
}

private function get_meta_item ( $item, $list ) {
	return array_key_exists( $item, $list ) ? $list[$item] : '';
}

private function write_single_listing ( $listing ) {
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
		$image_code = $image ? '<div class="list_img">' . $image .'</div>'.PHP_EOL : '';
	}

	// output listing
	ob_start();
?>
<li<?= $class; ?>>
	<?= $image_code; ?>
	<div class="list_body">
		<strong><?= $prefix, $title, $suffix; ?></strong>
		<?= $descr; ?>

	</div>
</li>

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


/**
 *	Retrieve Data from Files
 */

private function read_from_local_file ( $fname, $report_error ) {
	$contents = '';
	if ( file_exists( $fname ) ) {
		if ( filesize( $fname ) > 0 ) {
			$handle = fopen( $fname, 'r' );
			$contents = fread( $handle, filesize( $fname ) );
			fclose( $handle );
		}

	} elseif ( $report_error ) {
		mcw_log( "read_from_local_file: no such file: $fname" );
	}
	return $contents;
}

private function read_from_remote_file ( $fname ) {
	return null;
}

private function read_from_file ( $fname, $report_error=true ) {
	if ( $this->is_coloma ) {
		return $this->read_from_local_file( $fname, $report_error );
	} else {
		return $this->read_from_remote_file( $fname, $report_error );
	}
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

private function get_cat_heading ( $cat, $heading=true ) {
	if ( $heading === true ) {
		$heading = $cat['name'];
	}
	ob_start();
?>
	<h2 class="jump-target" id="dircat_<?= $cat['term_id']; ?>"><?= $heading; ?></h2>

<?php
	return ob_get_clean();
}


private function retrieve_jump_links () {
	return $this->read_from_file( INCLUDES_PATH . 'jumplinks.html' );
}


private function retrieve_catlist () {
	$catlist_json = $this->read_from_file( INCLUDES_PATH . 'catlist.json' );
	return json_decode( $catlist_json, true );
}


/*
	$heading:
		false or null value => don't display any heading
		boolean true => display cat name
		any other value => display that value
*/
private function retrieve_cat_listing ( $cat, $heading=false ) {
	ob_start();

	if ( $heading ) {
		echo $this->get_cat_heading( $cat, $heading );
	}
?>
	<ul class="dir_listings">

<?php
	// look for premium 0 listings; don't report error
	$p0_json = $this->read_from_file( INCLUDES_PATH . "{$cat['slug']}_0.json", false );
	if ( $p0_json ) {
		$p0_array = json_decode( $p0_json, true );
		$nr_items = sizeof( $p0_array );

		// start at random point in the premium 0 array
		$initial = mt_rand( 0, $nr_items-1 );
		for ( $i = $initial; $i < $nr_items; $i++ ) {
			echo $p0_array[$i];
		}
		// now print from 1st of array to initial
		for ( $i = 0; $i < $initial; $i++ ) {
			echo $p0_array[$i];
		}
	}

	// print the rest of the listings
	echo $this->read_from_file( INCLUDES_PATH . "{$cat['slug']}.html" );
?>
	</ul>

<?php
	return ob_get_clean();
} // retrieve_cat_listing


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