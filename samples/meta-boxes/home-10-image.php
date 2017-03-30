<?php
/*
Title: Front Page Feature Image
Description: Full-width feature image on the home page.
Post Type: page
ID: 2
Priority: high
Order: 10
Tab: Features
Flow: Homepage Workflow
*/

piklist('field', array(
	'type' => 'group',
	'field' => 'home_feature_image',
	'label' => 'Feature Image',
	'description' => 'Large image at the top of the main content area.',
	'add_more' => true,
	'template' => 'field',
	'columns' => 12,
	'fields' => array(
		array(
			'type' => 'file',
			'field' => 'section_image',
			'label' => 'Image',
			'description' => 'Add or upload an image.',
			'options' => array(
				'modal_title' => 'Add Image',
				'button' => 'Add Image',
			),
		),
		array(
			'type' => 'text',
			'field' => 'section_title',
			'label' => 'Image Title',
			'columns' => 12,
		),
		array(
			'type' => 'editor',
			'field' => 'section_content',
			'label' => 'Image Caption',
			'description' => 'Brief description of the image and its relevance to LCSO.',
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
			'validate' => array(
				array(
					'type' => 'url',
				),
			),
		),
	),
));
