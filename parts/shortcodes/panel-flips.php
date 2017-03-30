<?php
/*
Shortcode: panel-flips
*/

	$panel = get_post_meta( $post->ID, 'panel_flips', true );
//	if ( is_front_page() ) {
		$flips_per_row = 2;
		$col_class =  'col-sm-6 flip';
		$image_size = 'flip2';
/*
	} else {
		$flips_per_row = 3;
		$col_class =  'col-sm-4 flip';
		$image_size = 'flip3';
	}
*/

	// FIRST must exit the current container
?>
		</div><!-- container -->

<div class="flips container">
	<ul class="row panel_flips">

<?php
	$count = 0;
	foreach ( $panel as $column ) {

		// row break if needed
		if ( ++$count > $flips_per_row ) {
?>
	</ul>

	<ul class="row panel_flips">

<?php
			$count = 0;
		}

		extract( $column );
		$title = wptexturize( $flip_headline );
		$image = wp_get_attachment_image( $flip_image[0], $image_size, false, 'class=img-responsive' );
		$content = apply_filters( 'the_content', $flip_text );
		$link = mcw_get_anchor( $flip_link_url, wptexturize( $flip_link_text ), 'btn btn-default' );
?>
		<li class="<?php echo $col_class;?> item" ontouchstart="this.classList.toggle('hover');">
			<div class="front face">
				<?php echo $image; ?>
				<div class="flip-content">
					<h2><?php echo $title; ?></h2>
				</div>
			</div>
			<div class="back face">
				<?php echo $content; ?>
				<?php echo $link; ?>
			</div>
		</li>

<?php

	}
?>
	</ul>
</div><!-- flips container -->

<!-- restore state for next panel -->
		<div class="content-panel container">
