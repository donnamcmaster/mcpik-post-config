<?php
/*
Title: Related Pages
Post Type: project
Description: list of pages that are configured as related to this project
*/

$args = array(
	'post_type' => 'page', // Set post type you are relating to.
	'posts_per_page' => -1,
	'post_has' => $post->ID,
	'suppress_filters' => false, // This must be set to false
);
$list = get_posts( $args );
$s = mcw_get_posts_post_list( $list, ', ', 'link-edit' );
echo $s;
?>