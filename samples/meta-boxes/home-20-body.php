<?php
/*
Title: Feature Boxes
Post Type: page
Description: Row of feature boxes on the home page.
ID: 2
Priority: high
Order: 20
Tab: Features
Flow: Homepage Workflow
*/

piklist( 'field', array(
	'type' => 'group',
	'field' => 'home_feature_boxes',
	'label' => 'Row of Feature Boxes',
	'description' => 'Please fill in exactly three boxes.',
	'template' => 'field',
	'columns' => 12,
	'add_more' => true,
	'fields' => array(
		array(
			'type' => 'text',
			'field' => 'section_title',
			'columns' => 8,
			'label' => 'Box Title',
		),
		array(
			'type' => 'file',
			'field' => 'section_image',
			'label' => 'Box Image',
			'description' => 'Add or upload an image.',
			'options' => array(
				'modal_title' => 'Add Image',
				'button' => 'Add Image',
			),
		),
		array(
			'type' => 'editor',
			'field' => 'section_links',
			'label' => 'Page Links',
			'description' => 'List of links to related pages.',
			'help' => 'Please keep them in a list format.',
			'columns' => 12,
			'options' => array (
				'media_buttons' => false,
				'teeny' => true,
			),
		),
		array(
			'type' => 'text',
			'field' => 'section_link',
			'label' => 'Link to More Info',
			'description' => "If typing in a web address (URL), don't forget the 'http://'.",
			'columns' => 12,
		),
	),
));
