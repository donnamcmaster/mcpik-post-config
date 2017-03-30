<?php
/*
Name: Panel: Image Left
Description: Create a 2-column panel with an image in the left column and text in the right. 
Shortcode: panel-imgleft
Icon: dashicons-align-left
*/

piklist( 'field', array(
	'type' => 'file',
	'field' => 'image',
	'label' => 'Image for left column',
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

piklist( 'field', array(
	'type' => 'text',
	'field' => 'block_id',
	'label' => 'Unique name for right column text block',
));

piklist( 'field', array(
	'type' => 'editor',
	'field' => 'text',
	'label' => 'Content for right column',
	'columns' => 12,
	'options' => array (
		'media_buttons' => false,
		'teeny' => false,
		'quicktags' => true,
	),
));
