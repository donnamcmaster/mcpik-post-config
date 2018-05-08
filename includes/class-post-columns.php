<?php
/**
 *	Class McPik_Post_Type
 *
 *	General configuration and display routines for post types (custom and built-in).
 *
 *	@package McWebby_Base
 *	@subpackage Post_Type_Support
 *	@since McWebby Base 2.0
 */

Class McPik_Post_Type {

public $post_type = null;

/**
	$this->post_fields defines all custom fields, plus all built-in fields and 
	pseudo-fields such as "attached docs" used in column displays and multi-edit.
	Class McPik_Post_Type defines built-in fields; each subclass defines its custom fields.

	Supported Values 
	scope: post_meta, taxonomy, post (posts table), 
		(display only): calculate, thumbnail, attachments, children, ignore, 
	type: text, textarea, editor (wysiwyg), integer, url, email, select, checkboxes, 
		post_list, single_post, single_author, single_term, special, timestamp
		(display only): display_only, 
*/
protected
	$default_field = array (
		'scope' => 'post_meta',
		'type' => 'text',
		'col_display' => null,
		'null_ok' => true,
		'default' => '',
		'img_size' => 'thumbnail',

		// these fields are used only occasionally and must be null-checked
		'max_length' => null,
		'post_mime_type' => null,
		'post_type' => null,
		'taxonomy' => null,
		'text_align' => null,
		'options' => null,
	);

protected
	$post_fields = array(
		'ID' => array(
			'scope' => 'post',
			'db_field' => 'ID',
			'input_type' => 'text',
		),
		'post_title' => array(
			'scope' => 'post',
			'db_field' => 'post_title',
			'type' => 'text',
		),
		'post_status' => array(
			'scope' => 'post',
			'db_field' => 'post_status',
			'type' => 'text',
		),
		'post_content' => array(
			'scope' => 'post',
			'db_field' => 'post_content',
			'type' => 'textarea',
		),
		'post_excerpt' => array(
			'scope' => 'post',
			'db_field' => 'post_excerpt',
			'type' => 'textarea',
		),
		'post_date' => array(
			'scope' => 'post',
			'db_field' => 'post_date',
			'type' => 'text',
		),
		'slug' => array(
			'scope' => 'post',
			'db_field' => 'post_name',
			'type' => 'text',
		),
		'post_parent' => array(
			'scope' => 'post',
			'db_field' => 'post_parent',
			'type' => 'single_post',
			'post_type' => null,	// init when handler created
		),
		// same as post_parent but display only
		'display_parent' => array(
			'scope' => 'post',
			'db_field' => 'post_parent',
			'type' => 'display_only',
			'post_type' => null,	// init when handler created
		),
		'menu_order' => array(
			'scope' => 'post',
			'db_field' => 'menu_order',
			'type' => 'text',
		),
		'post_author' => array(
			'scope' => 'post',
			'db_field' => 'post_author',
			'type' => 'single_author',
			'null_ok' => false,
		),
		'_wp_page_template' => array(
			'scope' => 'post_meta',
			'type' => 'text',
		),
		'_wp_attachment_image_alt' => array(
			'scope' => 'post_meta',
			'type' => 'text',
		),
		'thumb' => array(	// for featured image ("post thumbnail")
			'scope' => 'thumbnail',
			'img_size' => 'thumbnail',	// override for different size in col display
			'col_display' => 'image',
		),
		'attached_docs' => array(
			'scope' => 'attachments',
			'post_mime_type' => 'application',
		),
		'image_count' => array(
			'scope' => 'calculate',
			'post_mime_type' => 'image',
		),
		'img_size' => array(
			'scope' => 'calculate',
		),
		'attachment_count' => array(
			'scope' => 'calculate',
			'post_mime_type' => '',
		),
	);

protected $custom_columns = null;		// columns for group edit display

/**
 *
 *	Sub-Class Initialization and Registration
 *
 *	On the init hook, function 
 *	calls McPik_Post_Type::init_post_type for each post_type defined by child theme. 
 */


/**
 *	init_post_type
 *
 *	Called by each subclass object's __construct method 
 *	- basic instance init
 *	- sets action hooks and filters
 */
protected function init_post_type ( $post_type, $type_name=null, $display_name=null ) {
	$this->post_type = $post_type;
	$this->type_name = $type_name ? $type_name : $post_type;
	$this->display_name = $display_name ? $display_name : ucwords( $this->type_name );

	// override in post type handler init if post_parent is of a different type
	$this->post_fields['post_parent']['post_type'] = $post_type;
	$this->post_fields['display_parent']['post_type'] = $post_type;

	if ( is_admin() ) {
		// initialize common admin filters
		if ( $this->post_type == 'attachment' ) {
			$filter = 'manage_media_columns';
		} else {
			$filter = 'manage_'.$this->post_type.'_posts_columns';
		}
		add_filter( $filter, array( $this, 'manage_columns' ) );
	}

	// initialize subclass filters if defined
	if ( method_exists( $this, 'init_filters_and_actions' ) ) {
		$this->init_filters_and_actions();
	}
}


// expects a list of post objects
protected static function get_posts_post_list ( $post_list, $separator=', ', $link='' ) {
	$s = '';
	if ( $post_list ) {
		$sep = '';
		foreach ( $post_list as $p ) {
			$s .= $sep . $p->post_title;
			$sep = $separator;
		}
	}
	return $s;
}


/**
 *
 *	Display Methods
 *
 */

/**
 *	get_linked_name
 *	for post types with URLs associated (links to websites)
 *	returns an anchor with the post title
 *	pass the name if calling function already has it, to avoid add'l db lookup
 */
public function get_linked_name ( $post_id, $name='' ) {
	$url = get_permalink( $post_id );
	$name = $name ? $name : get_the_title( $post_id );
	if ( !$name ) {
		mcw_log( "no name found for get_linked_name ( $post_id )" );
		return null;
	} else {
		return $url ? McPik_Utils::get_anchor( $url, $name ) : $name;
	}
}


/**
 *
 *	Admin: Columns
 *
 */

public function manage_columns ( $defaults ) {
    return $defaults;
}

public function custom_column ( $column_name, $id ) {
	global $wpdb;
	if ( !array_key_exists( $column_name, $this->post_fields ) ) {
		return;
	}
	$post = get_post( $id );

	$column = wp_parse_args( $this->post_fields[$column_name], $this->default_field );
	extract( $column );
	switch ( $scope ) {
   		case 'post':
			echo $post->$db_field;
			break;

   		case 'related':
			switch ( $type ) {
				case 'single_post':
				case 'post_list':
					$args = array(
						'post_type' => $post_type, // Set post type you are relating to.
						'posts_per_page' => -1,
						'post_belongs' => $id,
						'suppress_filters' => false, // This must be set to false
					);
					$list = get_posts( $args );
					$s = self::get_posts_post_list( $list );
					echo $s;
					break;
				case 'post_related':
					$args = array(
						'post_type' => $post_type, // Set post type you are relating to.
						'posts_per_page' => -1,
						'post_has' => $id,
						'suppress_filters' => false, // This must be set to false
					);
					$list = get_posts( $args );
					$s = self::get_posts_post_list( $list );
					echo $s;
					break;
			}

		case 'post_meta':
			// handles only a single post selection
			// special case implementation for photographer
			// needs to be handled more broadly
			if ( ( $type == 'select' ) && isset( $post_type ) ) {
				$selected_post_id = get_post_meta( $id, $column_name, true );
				if ( $selected_post_id ) {
					$selected_post = get_post( $selected_post_id );
					if ( $selected_post ) {
						echo $selected_post->post_title;
					}
				}
			// handle case that may have multiple results
			} elseif ( isset( $choices ) ) {
				$meta = get_post_meta( $id, $column_name );
				if ( !is_array( $meta ) ) {
					echo $choices[$meta];

				} else {
					if ( is_array( $meta[0] ) ) {
						$meta = $meta[0];
					}
					foreach ( $meta as $key ) {
						if ( isset( $choices[$key] ) ) {
							echo $choices[$key], ', ';
						} else {
							echo "unknown option $key, ";
						}
					}
				}

			} else {
				$meta = get_post_meta( $id, $column_name, true );
				if ( ( $type == 'file' ) && ( $col_display == 'image' ) ) {
					// here $meta represents the attachment ID(s)
					if ( is_array( $meta ) ) {
						echo 'multiple';
					} elseif ( $meta ) {
						echo wp_get_attachment_image( $meta, $img_size );
					} else {
						echo '-';
					}
				} elseif ( $type == 'timestamp' ) {
	   				echo date('n/j/Y g:i A', $meta );
				} else {
	   				echo $meta;
				}
			}
			break;

   		case 'taxonomy':
	    	$cats = wp_get_object_terms( $id, $column_name );
	    	if ( $cats && is_array( $cats ) ) {
				foreach ( $cats as $cat ) {
					echo $cat->name,', ';
				}	    	
			}	    	
			break;
 
   		case 'thumbnail':
			$thumb_src = null;
			// for images in media library
			if ( $post->post_type == 'attachment' ) {
				$thumb_src = wp_get_attachment_image_src( $id, $img_size, false );

   			// for "featured image" aka "post thumbnail"
			} elseif ( $thumb_id = get_post_meta( $id, '_thumbnail_id', true ) ) {
				$thumb_src = wp_get_attachment_image_src( $thumb_id, $img_size, false );
			}

			if ( $thumb_src ) {
				echo '<img src="' . $thumb_src[0] .'" alt="feature image" />';
			} else {
				echo '-';
			}
			break;
 
   		case 'calculate':
   			switch ( $column_name ) {
   				case 'attachment_count':
   					$where = "post_type='attachment' AND post_parent=$id";
					$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE $where" );
   					echo $count;
   					break;
   				case 'image_count':
   					$where = "post_type='attachment' AND post_parent=$id AND post_mime_type LIKE '%image%'";
					$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE $where" );
   					echo $count;
   					break;
				case 'img_size':
					if ( wp_attachment_is_image( $id ) ) {
						list( $img_src, $width, $height ) = image_downsize( $id, 'full' );
						echo $width, ' x ', $height;
					}
					break;
   				default:
					$this->special_custom_column( $column_name, $id, $post );			
   			}
			break;
		default:
			$this->special_custom_column( $column_name, $id, $post );			
    } // switch
}

protected function special_custom_column ( $column_name, $id, $post ) {
	// override this function for post_type specific columns
}


} // Class McPik_Post_Type
?>