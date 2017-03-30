<?php
/*
Title: Home Page Sidebar "About"
Post Type: page
ID: 2
Description: Top sidebar section for home page.
Priority: high
Order: 40
Tab: Sidebar
Flow: Homepage Workflow
*/

piklist('field', array(
	'type' => 'group',
	'field' => 'home_sidebar_about',
	'label' => 'About Us Section',
	'description' => 'Top sidebar section, featuring the Sheriff.',
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
			'type' => 'editor',
			'field' => 'section_content',
			'label' => 'Description',
			'description' => 'Brief description of LCSO.',
			'columns' => 12,
			'options' => array (
				'media_buttons' => false,
				'teeny' => true,
			),
		),
		array(
			'type' => 'file',
			'field' => 'sheriff_photo',
			'label' => "Sheriff's Photo",
			'description' => 'Add or upload a photo (e.g., of the Sheriff).',
			'options' => array(
				'modal_title' => 'Add Image',
				'button' => 'Add Image',
			),
		),
		array(
			'type' => 'text',
			'field' => 'sheriff_name',
			'label' => "Sheriff's Name",
			'columns' => 12,
		),
		array(
			'type' => 'text',
			'field' => 'sheriff_title',
			'label' => "Sheriff's Title",
			'value' => 'Linn County Sheriff',
			'columns' => 12,
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
