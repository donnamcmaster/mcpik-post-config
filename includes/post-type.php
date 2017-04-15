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
		post_list, single_post, single_author, single_term, special, 
		(display only): display_only, 
*/
protected
	$default_field = array (
		'scope' => 'post_meta',
		'type' => 'text',
		'col_title' => 'Item',	// for columns or multi-edit display
		'col_align' => '',
		'col_width' => null,
		'col_display' => null,
		'label' => 'Enter value: ',
		'help' => '',		// optional sub-text for label
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
			'col_title' => 'ID',
			'col_width' => 'small',
			'input_type' => 'text',
		),
		'post_title' => array(
			'scope' => 'post',
			'db_field' => 'post_title',
			'col_title' => 'Title',
			'col_width' => 'medium',
			'type' => 'text',
		),
		'post_status' => array(
			'scope' => 'post',
			'db_field' => 'post_status',
			'col_title' => 'Status',
			'col_width' => 'medium',
			'type' => 'text',
		),
		'post_content' => array(
			'scope' => 'post',
			'db_field' => 'post_content',
			'col_title' => 'Content',
			'col_width' => 'wide',
			'type' => 'textarea',
		),
		'post_excerpt' => array(
			'scope' => 'post',
			'db_field' => 'post_excerpt',
			'col_title' => 'Excerpt',
			'col_width' => 'wide',
			'type' => 'textarea',
		),
		'post_date' => array(
			'scope' => 'post',
			'db_field' => 'post_date',
			'col_title' => 'Post Date',
			'col_width' => 'medium',
			'type' => 'text',
		),
		'slug' => array(
			'scope' => 'post',
			'db_field' => 'post_name',
			'col_title' => 'Slug',
			'col_width' => 'medium',
			'type' => 'text',
		),
		'post_parent' => array(
			'scope' => 'post',
			'db_field' => 'post_parent',
			'col_title' => 'Parent',
			'col_width' => 'medium',
			'type' => 'single_post',
			'post_type' => null,	// init when handler created
		),
		// same as post_parent but display only
		'display_parent' => array(
			'scope' => 'post',
			'db_field' => 'post_parent',
			'col_title' => 'Parent',
			'col_width' => 'medium',
			'type' => 'display_only',
			'post_type' => null,	// init when handler created
		),
		'menu_order' => array(
			'scope' => 'post',
			'db_field' => 'menu_order',
			'col_title' => 'Menu Order',
			'col_width' => 'tiny',
			'type' => 'text',
		),
		'post_author' => array(
			'scope' => 'post',
			'db_field' => 'post_author',
			'col_title' => 'Author',
			'col_width' => 'small',
			'label' => 'Select a new author:',
//			'help' => '(WordPress only allows selecting from editors; use this selection box to override and assign a new author.)',
			'type' => 'single_author',
			'null_ok' => false,
		),
		'_wp_page_template' => array(
			'scope' => 'post_meta',
			'col_title' => 'Template',
			'col_width' => 'small',
			'type' => 'text',
		),
		'thumb' => array(	// for featured image ("post thumbnail")
			'scope' => 'thumbnail',
			'img_size' => 'thumbnail',	// override for different size in col display
			'col_title' => 'Image',
			'col_width' => 'narrow',
			'col_display' => 'image',
		),
		'attached_docs' => array(
			'scope' => 'attachments',
			'label' => 'Attached Document Files:',
			'col_title' => 'Files',
			'col_width' => 'narrow',
			'post_mime_type' => 'application',
		),
		'image_count' => array(
			'scope' => 'calculate',
			'label' => 'Image Count:',
			'col_title' => 'Image Count',
			'col_width' => 'narrow',
			'post_mime_type' => 'image',
		),
		'img_size' => array(
			'scope' => 'calculate',
			'col_title' => 'Size',
		),
		'attachment_count' => array(
			'scope' => 'calculate',
			'label' => 'Attachment Count:',
			'col_title' => 'Attachment Count',
			'col_width' => 'narrow',
			'post_mime_type' => '',
		),
	);

protected $custom_columns = null;		// columns for group edit display
protected $contextual_help = null;		// help text for post edit screen
protected $meta_boxes = null;

protected $default_sort_by = 'menu_order';
protected $default_sort_order = 'ASC';
protected $default_sort_meta_key = '';

protected $url_field = null;			// opt field containing URL for this post type

protected $registered = false;			// has this post type been registered?


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

	if ( !$this->registered ) {
		add_filter( 'piklist_post_types', array( $this, 'register' ) );
	}

	if ( is_admin() ) {
		// initialize common admin filters
		if ( $this->post_type == 'attachment' ) {
			$filter = 'manage_media_columns';
		} else {
			$filter = 'manage_'.$this->post_type.'_posts_columns';
		}
		add_filter( $filter, array( $this, 'manage_columns' ) );

		if ( method_exists( $this, 'post_updated_messages' ) ) {
			add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		}
		if ( $this->contextual_help ) {
			add_action( 'contextual_help', array( $this, 'add_help_text' ), 10, 3 );
		}
	}

	// initialize subclass filters if defined
	if ( method_exists( $this, 'init_filters_and_actions' ) ) {
		$this->init_filters_and_actions();
	}
}


/**
 *	get_handler
 *
 *	Returns the handler object for $post_type. 
 */
public static function get_handler ( $post_type ) {
	global $mcpk_pt_handlers;
	return $mcpk_pt_handlers[$post_type];
}


/**
 *	register
 *
 *	Hooked to Piklist's 'piklist_post_types' filter. 
 *	- registers the custom post type; sets registered=true
 */
public function register ( $post_types ) {
	if ( !$this->registered ) {
		$labels = piklist( 'post_type_labels', $this->display_name );
		$this->register_args['labels'] = $labels;
		$post_types[$this->post_type] = $this->register_args;
		$this->registered = true;
	}
	return $post_types;
}


/**
 *	Get Details
 *	- returns a simple array with post meta and the essential post content
 *		ID
 *		post_title
 *		post_status
 *		post_date
 *		post_excerpt
 *		post_content
 */
protected static function get_details ( $post, $first_element_only=false ) { 
	$details = array();
	
	// get post meta
	$details = mcw_get_simple_post_custom( $post->ID, $first_element_only );

	// add info from post record
	$details['ID'] = $post->ID;
	$details['post_title'] = $post->post_title;
	$details['post_status'] = $post->post_status;
	$details['post_date'] = $post->post_date;
	$details['post_excerpt'] = $post->post_excerpt;
	$details['post_content'] = $post->post_content;

	return $details;
}


/**
 *	Get Post Choices
 *	- returns a selection array of post IDs & names
 */
static function get_post_choices ( $post_type, $null_ok=false, $null_label='-- none --', $limit=-1 ) { 
	$choices = piklist(
		get_posts( array(
			'post_type' => $post_type,
			'numberposts' => $limit,
			'orderby' => 'title',
			'order' => 'ASC'
		)),
		array( 'ID', 'post_title' )
	);
	if ( $null_ok ) {
		$null_choice = array( 0 => $null_label );
		$choices = $null_choice + $choices;
	}
	return $choices;
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
public function get_linked_name ( $id, $name='' ) {
	$url = $this->url_field ? get_post_meta( $id, $this->url_field, true) : '';
	$name = $name ? $name : get_the_title( $id );
	return $url ? mcw_get_anchor( $url, $name ) : $name;
}


/**
 *	piklist_meta_box_fields
 *	Calls Piklist to create the fields for a meta box. 
 *	No return value.
 */
public function piklist_meta_box_fields ( $meta_box ) {
	if ( !is_array( $this->meta_boxes ) || !isset( $this->meta_boxes[$meta_box] ) ) {
		mcw_log( "piklist_meta_box_fields: no meta_box $meta_box" );
		return;
	}
	foreach ( $this->meta_boxes[$meta_box] as $field_name ) {
		$field = $this->post_fields[$field_name];
		$field['field'] = $field_name;
//piklist::pre($field);
		piklist( 'field', $field );
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
					$s = mcw_get_posts_post_list( $list );
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
					$s = mcw_get_posts_post_list( $list );
					echo $s;
					break;
			}

		case 'post_meta':
			// handle case that may have multiple results
			if ( isset( $choices ) ) {
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