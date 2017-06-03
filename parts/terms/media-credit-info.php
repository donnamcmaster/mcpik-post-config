<?php
/*
Title: Media Credit Details
Description: 
Taxonomy: media_credit
New: true
*/

piklist( 'field', array(
	'type' => 'text',
	'field' => 'credit_url',
	'label' => "Photographer's website",
	'description' => 'Enter web address, beginning with http://',
	'attributes' => array(
		'placeholder' => 'http://',
		'class' => 'regular-text',
	),
));

piklist( 'field', array(
	'type' => 'file',
	'field' => 'credit_image',
	'label' => 'Image for Credits Page',
	'description' => 'Needs to be at least 400px wide by 300px tall. Will be cropped to a 4x3 rectangle.',
	'options' => array(
		'modal_title' => 'Add Image',
		'button' => 'Add Image',
	)
));

piklist( 'field', array(
	'type' => 'editor',
	'field' => 'scratchpad',
	'label' => __( 'Scratchpad' ),
	'columns' => '12',
	'options' => array (
		'media_buttons' => true,
		'textarea_rows' => 8,
		'teeny' => false,
		'quicktags' => true,
	),
));