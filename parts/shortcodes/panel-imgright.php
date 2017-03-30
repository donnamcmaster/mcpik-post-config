<?php
/*
Shortcode: panel-imgright
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

	$content = do_shortcode( shortcode_unautop( wpautop( wptexturize( $text ) ) ) );

?>
	<div class="row panel_imgright">
		<div class="col-sm-5 box-image">
			<?php echo $image; ?>
		</div>
		<div class="col-sm-7 box-text">
			<?php echo $content; ?>
		</div>
	</div>
