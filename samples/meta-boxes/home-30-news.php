<?php
/*
Title: Featured News
Post Type: page
Description: Recent news posts featured on the home page.
ID: 2
Priority: high
Order: 30
Tab: Features
Flow: Homepage Workflow
*/

piklist( 'field', array(
	'type' => 'group',
	'field' => 'home_news',
	'label' => 'Featured News',
	'description' => 'Information for the home page news feature.',
	'template' => 'field',
	'columns' => 12,
	'fields' => array(
		array(
			'type' => 'text',
			'field' => 'section_title',
			'columns' => 12,
			'label' => 'News Section Title',
			'value' => "What's New",
		),
		array(
			'type' => 'number',
			'field' => 'section_quantity',
			'label' => 'Number of Posts',
			'description' => 'How many recent posts should we display?',
			'value' => 2,
			'validate' => array(
				array(
					'type' => 'range',
					'options' => array(
						'min' => 1,
						'max' => 4,
					),
				),
			),
		),
		array(
			'type' => 'text',
			'field' => 'section_link_text',
			'label' => 'Text for Link to News Page',
			'value' => 'View All News',
			'columns' => 8,
			'validate' => array(
				array(
					'type' => 'safe_text',
				),
			),
		),
	),
));
