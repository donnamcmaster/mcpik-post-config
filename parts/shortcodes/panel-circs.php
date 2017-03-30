<?php
/*
Shortcode: panel-circles
*/

	$panel = get_post_meta( $post->ID, 'panel_circs', true );
	if ( !$panel ) {
		return;
	}
	// FIRST must exit the current container
?>
		</div><!-- container -->

<ul class="circs container">
	<div class="row">

<?php
	$count = 0;
	foreach ( $panel as $column ) {
		// row break if needed
		if ( ++$count > 3 ) {
?>
	</div><!-- row -->

	<div class="row">

<?php
			$count = 0;
		}
		extract( $column );
		$title = wptexturize( $feature_headline );
		$content = apply_filters( 'the_content', $feature_text );
		$image = wp_get_attachment_image( $feature_image[0], 'circ3', false, 'class=img-responsive' );
		
		if ( isset( $feature_link_url ) && $feature_link_url ) {
			$title = mcw_get_anchor( $feature_link_url, $title );
			$image = mcw_get_anchor( $feature_link_url, $image );
			$link = $feature_link_text ? mcw_get_anchor( $feature_link_url, wptexturize( $feature_link_text ), 'btn btn-default' ) : '';
		} else {
			$link = '';
		}
?>
	<div class="col-sm-4 item">
		<?php echo $image; ?>
		<h2><?php echo $title; ?></h2>
		<?php echo $content; ?>
		<?php echo $link; ?>
	</div>

<?php

	}
?>
	</div><!-- row -->
</ul><!-- circs container -->

<!-- restore state for next panel -->
		<div class="content-panel container">
