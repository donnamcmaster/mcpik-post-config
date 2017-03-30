<?php
/*
Title: Home Feature Boxes
Post Type: page
Description: Row of feature boxes on the home page.
ID: 4
Priority: high
Order: 20,
*/

piklist('field', array(
	'type' => 'group',
	'field' => 'home_features',
	'label' => 'Home Features',
	'description' => 'Each feature includes an image, title and link. If there are multiple images for a feature, one of them will be chosen randomly each time the page loads.',
	'columns' => 12,
	'add_more' => true,
	'fields' => array(
		array(
			'type' => 'text',
			'field' => 'home_feature_title',
			'label' => 'Title',
		),
		array(
			'type' => 'text',
			'field' => 'home_feature_link',
			'label' => 'Link',
		),
		array(
			'type' => 'file',
			'field' => 'home_feature_image',
			'label' => 'Add File(s)',
			'options' => array(
				'modal_title' => 'Add File(s)',
				'button' => 'Add',
			),
		),
	),
));