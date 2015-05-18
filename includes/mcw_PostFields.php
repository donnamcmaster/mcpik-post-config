<?php
/**
 *	Class mcw_PostFields
 *
 *	General configuration and display routines for post types (custom and built-in).
 *
 *	@package McWebby_Base
 *	@subpackage Post_Type_Support
 *	@since McWebby Base 2.0
 */

Class mcw_PostFields {

public $post_type = null;

/**
	$this->post_fields defines all custom fields, plus all built-in fields and 
	pseudo-fields such as "attached docs" used in column displays and multi-edit.
	Class mcw_PostFields defines built-in fields; each subclass defines its custom fields.

	Supported Values 
	storage: meta, taxonomy, post_db, 
		(display only): calculate, thumbnail, attachments, children, ignore, 
	input_type: text, textarea, wysiwyg, integer, url, email, select, checkboxes, 
		post_list, single_post, single_author, single_term, special, 
		(display only): display_only, 
*/
protected
	$default_field = array (
		'storage' => 'meta',
		'input_type' => 'text',
		'col_title' => 'Item',	// for columns or multi-edit display
		'col_align' => '',
		'col_width' => null,
		'prompt' => 'Enter value: ',
		'help_text' => '',		// optional sub-text for prompt
		'null_ok' => true,
		'default' => '',
		'editor_id' => 'mcwebby',	// for wysiwyg

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
		'post_title' => array(
			'storage' => 'post_db',
			'db_field' => 'post_title',
			'col_title' => 'Title',
			'col_width' => 'medium',
			'input_type' => 'text',
		),
		'post_status' => array(
			'storage' => 'post_db',
			'db_field' => 'post_status',
			'col_title' => 'Status',
			'col_width' => 'medium',
			'input_type' => 'text',
		),
		'post_content' => array(
			'storage' => 'post_db',
			'db_field' => 'post_content',
			'col_title' => 'Content',
			'col_width' => 'wide',
			'input_type' => 'textarea',
		),
		'post_excerpt' => array(
			'storage' => 'post_db',
			'db_field' => 'post_excerpt',
			'col_title' => 'Excerpt',
			'col_width' => 'wide',
			'input_type' => 'textarea',
		),
		'post_date' => array(
			'storage' => 'post_db',
			'db_field' => 'post_date',
			'col_title' => 'Post Date',
			'col_width' => 'medium',
			'input_type' => 'text',
		),
		'slug' => array(
			'storage' => 'post_db',
			'db_field' => 'post_name',
			'col_title' => 'Slug',
			'col_width' => 'medium',
			'input_type' => 'text',
		),
		'post_parent' => array(
			'storage' => 'post_db',
			'db_field' => 'post_parent',
			'col_title' => 'Parent',
			'col_width' => 'medium',
			'input_type' => 'single_post',
			'post_type' => null,	// init when handler created
		),
		// same as post_parent but display only
		'display_parent' => array(
			'storage' => 'post_db',
			'db_field' => 'post_parent',
			'col_title' => 'Parent',
			'col_width' => 'medium',
			'input_type' => 'display_only',
			'post_type' => null,	// init when handler created
		),
		'menu_order' => array(
			'storage' => 'post_db',
			'db_field' => 'menu_order',
			'col_title' => 'Menu Order',
			'col_width' => 'tiny',
			'input_type' => 'text',
		),
		'post_author' => array(
			'storage' => 'post_db',
			'db_field' => 'post_author',
			'col_title' => 'Author',
			'col_width' => 'small',
			'prompt' => 'Select a new author:',
//			'help_text' => '(WordPress only allows selecting from editors; use this selection box to override and assign a new author.)',
			'input_type' => 'single_author',
			'null_ok' => false,
		),
		'_wp_page_template' => array(
			'storage' => 'meta',
			'col_title' => 'Template',
			'col_width' => 'small',
			'input_type' => 'text',
		),
		'thumb' => array(	// for featured image ("post thumbnail")
			'storage' => 'thumbnail',
			'img_size' => 'thumbnail',	// override for different size in col display
			'col_title' => 'Image',
			'col_width' => 'narrow',
		),
		'attached_docs' => array(
			'storage' => 'attachments',
			'prompt' => 'Attached Document Files:',
			'col_title' => 'Files',
			'col_width' => 'narrow',
			'post_mime_type' => 'application',
		),
		'image_count' => array(
			'storage' => 'calculate',
			'prompt' => 'Image Count:',
			'col_title' => 'Image Count',
			'col_width' => 'narrow',
			'post_mime_type' => 'image',
		),
		'img_size' => array(
			'storage' => 'calculate',
			'col_title' => 'Size',
		),
		'attachment_count' => array(
			'storage' => 'calculate',
			'prompt' => 'Attachment Count:',
			'col_title' => 'Attachment Count',
			'col_width' => 'narrow',
			'post_mime_type' => '',
		),
	);

protected $multi_edit_fields = null;	// fields to include in multi-edit
public $multi_edit_filters = null;		// filters for simplifying multi-edit

protected $custom_columns = null;		// columns for group edit display
protected $contextual_help = null;		// help text for post edit screen


/**
 *
 *	Sub-Class Initialization and Registration
 *
 *	On the init hook, function mcw_init_post_types (in McWebby Base functions.php)
 *	calls mcw_PostFields::init_custom_post_type for each post_type defined by child theme. 
 */


/**
 *	mcw_PostFields::create_handler
 *
 *	Called on the static mcw_PostFields superclass (not on a post_type object). 
 *	Creates a handler for the requested post type, which invokes the __construct 
 *	method for that class. 
 *	The new instance variable is stored in the global $mcw_cpf_handlers array. 
 */
public static function create_handler ( $post_type, $menu_order=null ) {
	global $mcw_cpf_handlers;

	// may already have been created; if so, we're done
	if ( array_key_exists( $post_type, $mcw_cpf_handlers ) ) {
		return;
	}

	$class_name = MCW_CPF_PREFIX . $post_type;
	$loaded_file = locate_post_type_file( $class_name, true );
	
	if ( !$loaded_file ) {
		mcw_log( 'mcw_PostFields: class file not found for '.$class_name, 'error' );
		return;
	}
	if ( !class_exists( $class_name ) ) {
		mcw_log( 'mcw_PostFields class '.$class_name.' not found for '.$post_type, 'error' );
		return;
	}

	$mcw_cpf_handlers[$post_type] = new $class_name();
}


/**
 *	init_post_fields
 *
 *	Called by each subclass object's __construct method 
 *	- basic instance init
 *	- sets action hooks and filters
 */
protected function init_post_fields ( $post_type ) {

	$this->post_type = $post_type;
	
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

		if ( $this->multi_edit_fields ) {
			add_action( 'admin_menu', array( $this, 'add_multi_edit_menu' ) );
		}

//		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		
		if ( $this->contextual_help ) {
			add_action( 'contextual_help', array( $this, 'add_help_text' ), 10, 3 );
		}
	}

	if ( isset( $this->post_thumbnails ) ) {
		foreach ( $this->post_thumbnails as $id=>$config ) {
			if ( $id == 'post-thumbnail' ) {
				set_post_thumbnail_size( $config['width'], $config['height'], $config['crop'] );
			} else {
				add_image_size( $id, $config['width'], $config['height'], $config['crop'] );
			}
		}
	}

	// initialize subclass filters if defined
	if ( method_exists( $this, 'init_filters_and_actions' ) ) {
		$this->init_filters_and_actions();
	}
}


/**
 *
 *	Methods to Return Class Variables
 *
 */

public function get_post_type () {
	return $this->post_type;
}
public function get_default_field () {
	return $this->default_field;
}
public function get_field ( $field ) {
	return $this->post_fields[$field];
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
	extract( $this->post_fields[$column_name] );
	switch ( $storage ) {
   		case 'post_db':
			echo $post->$db_field;
			break;
   		case 'related':
			switch ( $input_type ) {
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
   		case 'meta':
   			$meta = get_post_meta( $id, $column_name, true);
   			if ( $input_type == 'image' ) {
				// here $meta represents the attachment ID
				if ( $meta ) {
					$img_src = wp_get_attachment_image_src( $meta, $img_size, false );
					echo '<img src="' . $img_src[0] .'" alt="feature image" />';
				} else {
					echo '-';
				}
   			} else {
   				echo $meta;
   			}
			break;
   		case 'taxonomy':
	    	$cats = wp_get_object_terms( $id, $column_name );
	    	foreach ( $cats as $cat ) {
				echo $cat->name,', ';
			}	    	
			break;
   		case 'thumbnail':
   			// for "featured image" aka "post thumbnail"
			if ( $thumb_id = get_post_meta( $id, '_thumbnail_id', true ) ) {
				$thumb_src = wp_get_attachment_image_src( $thumb_id, $img_size, false );
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


/**
 *
 *	Admin: Notices
 *
 */

public function admin_notices () {
}


/**
 *	Admin: Contextual Help
 */
function add_help_text( $contextual_help, $screen_id, $screen ) { 
	if ( $this->post_type == $screen->id ) {
		$contextual_help = $this->contextual_help['edit_single'];
	} elseif ( 'edit-'.$this->post_type == $screen->id ) {
		$contextual_help = $this->contextual_help['all_posts'];
	}
	return $contextual_help;
}


/**
 *
 *	Admin: Multi-Edit (NOT CURRENTLY WORKING)
 *
 */
public function add_multi_edit_menu () {
	if ( $this->multi_edit_fields ) {
		add_submenu_page( 'edit.php?post_type='.$this->post_type, 'Multi-Edit '.$this->display_title_plural, 'Multi Edit', 'edit_posts', 'multi-edit-'.$this->post_type, array( $this, 'multi_edit' ) );
	}
}

public function multi_edit () {
?>
	<div class="wrap">
	<div id="icon-tools" class="icon32"><br /></div>
	<h2>Multi-Edit <?php echo $this->display_title_plural;?></h2>
	
	<p>Edit a group of <?php echo $this->display_plural;?>.</p>
	<p>Please contact Donna McMaster at <a href="mailto:donna@mcdonnas.com">donna@mcdonnas.com</a> or (541) 738-2973 if you need assistance. </p>
<?php

	if ( array_key_exists( 'update', $_POST ) ) {
		mcw_PostEdit::do_multi_update( $this->post_type, $this->multi_edit_fields, $this->post_fields, $this->multi_edit_filters, $this->display_title_plural );
	} elseif ( !isset( $this->multi_edit_filters ) ) {
		mcw_PostEdit::print_multi_edit_form( $this->post_type, $this->multi_edit_fields, $this->post_fields );
	} else {
		$filter_set = false;
		foreach ( $this->multi_edit_filters as $name => $info ) {
			$filter_index = 'mcw_filter_'.$this->post_type.'_'.$name;
			if ( key_exists( $filter_index, $_POST ) ) {
				$filter_set = $name;
				break;
			}
		}
		if ( $filter_set ) {
			mcw_PostEdit::print_multi_edit_form( $this->post_type, $this->multi_edit_fields, $this->post_fields, $this->multi_edit_filters, $filter_set );
		} else {
			mcw_PostEdit::prompt_multi_edit_filter( $this->post_type, $this->multi_edit_filters );
		}
	}

?>
	</div><!-- .wrap -->
<?php
}

} // Class mcw_PostFields
?>