<?php
/*
Title: Scratchpad
Post Type: page
Description: internal notes
Priority: low
*/
  
piklist( 'field', array(
	'field' => 'scratchpad',
	'type' => 'editor',
	'label' => __( 'Scratchpad' ),
	'description' => __( 'A place to save internal notes. Not publicly displayed.' ),
	'template' => 'field',
	'columns' => '12',
	'options' => array (
		'media_buttons' => true,
		'textarea_rows' => 8,
		'teeny' => false,
		'quicktags' => true,
	),
));