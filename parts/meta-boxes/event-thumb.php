<?php
/*
Title: Event Thumbnail
Description: thumbnail image for event listings
Post Type: event
Context: side
Order: 30
*/

piklist('field', array(
	'type' => 'file',
	'field' => 'event_thumb',
	'label' => 'for event lists',
	'description' => 'Needs to be at least 400px wide by 300px tall. Will be cropped to a 4x3 rectangle.',
	'options' => array(
		'modal_title' => 'Add Thumbnail Image',
		'button' => 'Add Thumbnail Image',
	)
));
