<?php
/*
Shortcode: panel-cols2
*/

	// inefficient because it's fixing stupid bugs and will hopefully be rewritten
	$text_blocks = get_post_meta( $post->ID, 'text_blocks', true );	
	if ( $text_blocks ) {
		if ( isset( $block_left ) && $block_left ) {
			// NOTE: will break early if finds a match
			foreach ( $text_blocks as $block ) {
				if ( $block['block_id'] == $block_left ) {
					$text_left = $block['block_text'];
					break;
				}
			} 
		}
		if ( isset( $block_right ) && $block_right ) {
			// NOTE: will break early if finds a match
			foreach ( $text_blocks as $block ) {
				if ( $block['block_id'] == $block_right ) {
					$text_right = $block['block_text'];
					break;
				}
			} 
		}
	}

	if ( substr( $text_left, 0, 4 ) == '</p>' ) {
		$text_left = substr_replace ( $text_left, '' , 0 ,4 );
	}
	if ( substr( $text_left, -3, 3 ) == '<p>' ) {
		$text_left = substr_replace ( $text_left, '' , -3 ,3 );
	}
	$text_left = wpautop( wptexturize( trim( $text_left ) ) );

	if ( substr( $text_right, 0, 4 ) == '</p>' ) {
		$text_right = substr_replace ( $text_right, '' , 0 ,4 );
	}
	if ( substr( $text_right, -3, 3 ) == '<p>' ) {
		$text_right = substr_replace ( $text_right, '' , -3 ,3 );
	}
	$text_right = wpautop( wptexturize( trim( $text_right ) ) );

?>
	<div class="row panel_cols2">
		<div class="col-sm-6 box-image">
			<?php echo $text_left; ?>
		</div>
		<div class="col-sm-6 box-text">
			<?php echo $text_right; ?>
		</div>
	</div>
