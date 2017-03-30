<?php
/*
Title: Section Name
Post Type: page
Capability: manage_options
Description: for pages at the top of a section; used in sidebar nav
Context: side
Priority: default
*/
  
if ( $post->ID == 2 ) {
	echo 'This is the home page; it is not in a section.';
	return;

} elseif ( $post->post_parent ) {
	$parent = get_post( $post->post_parent );
	echo 'Post parent is ', $parent->post_title, ' (', $post->post_parent, ').';
	return;
}

piklist('field', array(
	'type' => 'text',
	'field' => 'section_name',
	'label' => 'Section name:',
));

piklist('field', array(
	'type' => 'text',
	'field' => 'section_head',
	'label' => 'List this page in menus as:',
));
