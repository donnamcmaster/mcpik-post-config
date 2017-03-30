<?php
/*
Title: Content to be displayed above the form before it's successfully submitted
Post Type: page
Description: content to be displayed before form is successfully submitted
Priority: high
*/

piklist('field', array(
	'type' => 'editor',
	'template' => 'field',
	'field' => 'pre_form_content',
	'label' => 'Pre-Form Content',
	'description' => "Content to be displayed above the form before it's successfully submitted",
	'help' => "Content to be displayed above the form before it's successfully submitted",
	'options' => array (
		'media_buttons' => false,
		'textarea_rows' => 8,
	),
));
