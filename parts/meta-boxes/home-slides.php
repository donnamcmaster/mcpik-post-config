<?php
/*
Title: Home Page Sliders
Post Type: page
ID: 2
Priority: high
Order: 10
*/

piklist('field', array(
	'type' => 'group',
	'field' => 'home_slides',
	'label' => 'Large feature slides at the top of the page',
	'description' => 'Define a maximum of 4 slides. Please make sure that each image is at least 1186 by 565 pixels.',
	'columns' => 12,
	'add_more' => true,
	'fields' => array(
		array(
			'type' => 'text',
			'field' => 'headline',
			'label' => 'Headline',
		),
		array(
			'type' => 'file',
			'field' => 'image',
			'label' => 'Select Image',
			'options' => array(
				'modal_title' => 'Add Image',
				'button' => 'Add',
			),
		),
		array(
			'type' => 'editor',
			'field' => 'text',
			'label' => 'Text',
			'options' => array (
				'media_buttons' => false,
				'teeny' => true,
			),
			'validate' => array(
				array(
					'type' => 'limit',
					'options' => array(
						'max' => 32,
						'count' => 'words',
					),
				),
			),
		),
		array(
			'type' => 'text',
			'field' => 'link_text',
			'label' => 'Link Text',
			'value' => '',
		),
		array(
			'type' => 'text',
			'field' => 'link_url',
			'label' => 'Link URL',
			'help' => "If typing in a web address (URL), don't forget the 'http://'.",
			'value' => 'http://',
		),
	)
));
