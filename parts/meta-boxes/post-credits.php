<?php
/*
Title: Credits
Description: Credit byline for news author, links to photographers.
Post Type: post,page
Priority: high
Order: 10
*/

piklist( 'field', array(
	'field' => 'byline',
	'scope' => 'post_meta',
	'type' => 'editor',
	'label' => 'Optional Credit Line',
	'description' => 'E.g., "Thanks to Scott Armstrong of All-Outdoors" where "All-Outdoors" is a link to their site.',
	'options' => array (
		'media_buttons' => false,
		'teeny' => true,
		'textarea_rows' => 2,
		'quicktags' => true,
	) 
);
