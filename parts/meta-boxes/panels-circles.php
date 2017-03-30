<?php
/*
Title: Panels - Circles
Post Type: page
Template: template-panels
Priority: high
Order: 40
*/

piklist('field', array(
	'type' => 'group',
	'field' => 'panel_circs',
	'label' => 'Group of features with round images in 3 columns',
	'description' => 'Enter content for features. Ideally the quantity should be a multiple of 3.',
	'columns' => 12,
	'add_more' => true,
	'fields' => array(
		array(
			'type' => 'text',
			'field' => 'feature_headline',
			'label' => 'Headline',
		),
		array(
			'type' => 'file',
			'field' => 'feature_image',
			'label' => 'Select Image',
			'options' => array(
				'modal_title' => 'Add Image',
				'button' => 'Add',
			),
		),
		array(
			'type' => 'editor',
			'field' => 'feature_text',
			'label' => 'Text',
			'options' => array (
				'media_buttons' => false,
				'teeny' => true,
			),
		),
		array(
			'type' => 'text',
			'field' => 'feature_link_url',
			'label' => 'Link URL (optional)',
			'help' => "If typing in a web address (URL), don't forget the 'http://'.",
			'value' => 'http://',
		),
		array(
			'type' => 'text',
			'field' => 'feature_link_text',
			'label' => 'Link Text (optional)',
			'value' => 'Learn more',
		),
	),
));
