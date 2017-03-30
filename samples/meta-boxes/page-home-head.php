<?php
/*
Title: Front Page Header Images
Description: Full-width feature image on the home page.
Post Type: page
ID: 4
Priority: high
Order: 10
*/

piklist('field', array(
    'type' => 'file',
	'field' => 'home-header-image',
	'label' => 'Select images for the full-width home page feature',
	'description' => 'An image will be chosen randomly each time the page loads',
	'options' => array(
		'modal_title' => 'Add File(s)',
		'button' => 'Add',
	),
));
