<?php
/**
 *	Class mcw_PostFields_attachment
 *
 *	For the built-in post type, 'attachment'.
 *	Does NOT register the post type; it is a vehicle to extend it or customize display.
 *
 *	@package McWebby_Base
 *	@subpackage Post_Type_Support
 *	@since McWebby Base 2.0
 */

Class mcw_PostFields_attachment extends mcw_PostFields {

function __construct ( ) {
	parent::init_post_fields( 'attachment' );
	$this->registered = true;

	$this->post_fields['img_size'] = array(
		'storage' => 'calculate',
		'col_title' => 'Size',
	);
}

function init_filters_and_actions() {
}


/**
 *	Display Methods
 */


/**
 *	Admin Methods
 */

public function manage_columns ( $defaults ) {
    $defaults['img_size'] = __( 'Size' );
    return $defaults;
}

protected function special_custom_column ( $column_name, $id, $post ) {
	if ( !array_key_exists( $column_name, $this->post_fields ) ) {
		return;
	}
	extract( $this->post_fields[$column_name] );
	switch ( $column_name ) {
   		case 'img_size':
   			if ( wp_attachment_is_image( $id ) ) {
				list( $img_src, $width, $height ) = image_downsize( $id, 'full' );
				echo $width, ' x ', $height;
			}
			break;
	}
}

} // class mcw_PostFields_xxx
?>