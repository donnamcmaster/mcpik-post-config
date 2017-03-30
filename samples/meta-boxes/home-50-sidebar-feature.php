<?php
/*
Title: Home Page Sidebar Feature
Post Type: page
ID: 2
Description: Additional sidebar item for home page.
Priority: high
Order: 50
Tab: Sidebar
Flow: Homepage Workflow
*/

piklist('field', array(
	'type' => 'group',
	'field' => 'home_sidebar_feature',
	'label' => 'Sidebar Feature Section',
	'description' => 'Appears in the sidebar, below the About Us box.',
//	'add_more' => true,
	'template' => 'field',
	'columns' => 12,
	'fields' => array(
		array(
			'type' => 'text',
			'field' => 'section_title',
			'label' => 'Title',
			'columns' => 12,
		),
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
			'validate' => array(
				array(
					'type' => 'url',
				),
			),
		),
	),
));
