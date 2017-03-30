<?php
/*
Title: Panels - Flips
Post Type: page
Template: template-panels
Priority: high
Order: 30
*/

$flips_per_row = ( $post->ID == 2 ) ? 2 : 3;

piklist('field', array(
	'type' => 'group',
	'field' => 'panel_flips',
	'label' => "Group of flip boxes in $flips_per_row columns",
	'description' => "Enter content for features. The quantity must be a multiple of $flips_per_row.",
	'columns' => 12,
	'add_more' => true,
	'fields' => array(
		array(
			'type' => 'text',
			'field' => 'flip_headline',
			'label' => 'Headline',
		),
		array(
			'type' => 'file',
			'field' => 'flip_image',
			'label' => 'Select Image',
			'options' => array(
				'modal_title' => 'Add Image',
				'button' => 'Add',
			),
		),
		array(
			'type' => 'editor',
			'field' => 'flip_text',
			'label' => 'Text',
			'options' => array (
				'media_buttons' => false,
				'teeny' => true,
			),
			'validate' => array(
				array(
					'type' => 'limit',
					'options' => array(
						'max' => 40,
						'count' => 'words',
					),
				),
			),
		),
		array(
			'type' => 'text',
			'field' => 'flip_link_text',
			'label' => 'Link Text',
			'value' => '',
		),
		array(
			'type' => 'text',
			'field' => 'flip_link_url',
			'label' => 'Link URL',
			'help' => "If typing in a web address (URL), don't forget the 'http://'.",
			'value' => 'http://',
		),
	)
));
