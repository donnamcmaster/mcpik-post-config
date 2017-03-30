<?php
/*
Title: Personnel Details
Post Type: page
Description: Biographies and images for staff and board members.
Template: template-people
Priority: high
Order: 20
*/

if ( is_page( 'board-of-directors' ) ) {
	$label = 'Board Members';
} else {
	$label = 'Personnel';
}

piklist('field', array(
	'type' => 'group',
	'field' => 'board_members',
	'label' => $label,
	'description' => '',
	'add_more' => true,
	'template' => 'field',
	'columns' => 12,
	'fields' => array(
		array(
			'type' => 'text',
			'field' => 'person_name',
			'label' => 'Name',
			'columns' => 12,
		),
		array(
			'type' => 'text',
			'field' => 'person_title',
			'label' => 'Title (if applicable)',
			'columns' => 12,
		),
		array(
			'type' => 'editor',
			'field' => 'person_bio',
			'label' => __( "Person's Bio" ),
			'description' => __( 'Brief biography'),
			'columns' => 12,
			'options' => array (
				'media_buttons' => false,
				'teeny' => true,
			),
		),
		array(
			'type' => 'file',
			'field' => 'person_image',
			'label' => 'Add an image',
			'options' => array(
				'modal_title' => 'Add Image',
				'button' => 'Add',
			),
		),
	),
));
