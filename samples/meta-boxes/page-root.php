<?php
/*
Title: Root Page
Post Type: page
Capability: manage_options
Description: for pages with no parent
Context: side
Priority: high
*/

if ( $post->post_parent ) {
	$parent = get_post( $post->post_parent );
	echo 'Post parent is ', $parent->post_title, ' (', $post->post_parent, ').';
	return;
} elseif ( is_front_page() ) {
	echo 'This is the home page (', $post->ID, ').';
	return;
}

$choices = array(
	0 => 'Do not show secondary menus for this section.',
	$post->ID => "This is the root page for this section's secondary menu.",
	MCW_HOME_PAGE_ID => 'For secondary menus, this page is under the home page section.',
);
piklist('field', array(
	'type' => 'radio'
	,'field' => 'root_page'
	,'scope' => 'post_meta'
	,'label' => 'Secondary Menus for This Section'
	,'choices' => $choices
));