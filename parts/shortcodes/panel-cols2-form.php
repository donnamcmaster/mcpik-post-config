<?php
/*
Name: Panel: Two Columns
Description: Create a panel with two text columns of equal width. 
Shortcode: panel-cols2
Icon: dashicons-controls-pause
*/

piklist( 'field', array(
	'type' => 'editor',
	'field' => 'text_left',
	'label' => 'Content for left column',
	'columns' => 12,
	'options' => array (
		'media_buttons' => false,
		'teeny' => true,
	),
));

piklist( 'field', array(
	'type' => 'text',
	'field' => 'block_left',
	'label' => 'Unique name for left column text block',
));

piklist( 'field', array(
	'type' => 'editor',
	'field' => 'text_right',
	'label' => 'Content for right column',
	'columns' => 12,
	'options' => array (
		'media_buttons' => false,
		'teeny' => true,
	),
));

piklist( 'field', array(
	'type' => 'text',
	'field' => 'block_right',
	'label' => 'Unique name for right column text block',
));
