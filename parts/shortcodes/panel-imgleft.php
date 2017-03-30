<?php
/*
Shortcode: panel-imgleft
*/

	if ( isset( $block_id ) && $block_id ) {
		$text_blocks = get_post_meta( $post->ID, 'text_blocks', true );	
		if ( $text_blocks ) {
			// NOTE: will break early if finds a match
			foreach ( $text_blocks as $block ) {
				if ( $block['block_id'] == $block_id ) {
					$text = $block['block_text'];
					break;
				}
			} 
		}
	}

	$image = wp_get_attachment_image( $image, 'img-col', false, 'class=img-responsive' );
	if ( isset( $image_url ) && $image_url ) {
		$image = mcw_get_anchor( $image_url, $image );
	}
//piklist::pre( $text );

//	$content = do_shortcode( shortcode_unautop( wpautop( wptexturize( $text ) ) ) );
	if ( substr( $text, 0, 4 ) == '</p>' ) {
		$text = substr_replace ( $text, '' , 0 ,4 );
	}
	if ( substr( $text, -3, 3 ) == '<p>' ) {
		$text = substr_replace ( $text, '' , -3 ,3 );
	}

	$content = wpautop( wptexturize( trim( $text ) ) );

?>
	<div class="row panel_imgleft">
		<div class="col-sm-5 box-image">
			<?php echo $image; ?>
		</div>
		<div class="col-sm-7 box-text">
			<?php echo $content; ?>
		</div>
	</div>
