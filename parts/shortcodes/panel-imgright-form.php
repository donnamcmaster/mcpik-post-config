<?php
/*
Name: Panel: Image Right
Description: Create a 2-column panel with text in the left column and an image in the right. 
Shortcode: panel-imgright
Icon: dashicons-align-right
*/

piklist( 'field', array(
	'type' => 'text',
	'field' => 'block_id',
	'label' => 'Unique name for left column text block',
));

piklist( 'field', array(
	'type' => 'editor',
	'field' => 'text',
	'label' => 'Content for left column',
	'columns' => 12,
	'options' => array (
		'media_buttons' => false,
		'teeny' => true,
		'quicktags' => true,
	),
));

piklist( 'field', array(
	'type' => 'file',
	'field' => 'image',
	'label' => 'Image for right column',
	'description' => 'Choose or upload a single image.',
	'options' => array(
		'modal_title' => 'Add Image',
		'button' => 'Add Image',
	),
	'validate' => array(
		array(
			'type' => 'limit',
			'options' => array(
				'min' => 0,
				'max' => 1,
			),
		),
	),
));

piklist( 'field', array(
	'type' => 'text',
	'field' => 'image_url',
	'label' => 'Image Link',
	'description' => 'If you want the image to link to a page, enter the URL here.',
	'help' => "When typing in a web address (URL), don't forget the 'http://'.",
	'placeholder' => 'http://',
));

