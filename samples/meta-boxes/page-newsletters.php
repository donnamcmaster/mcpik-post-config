<?php
/*
Title: Document Archive
Post Type: page
Description: List of newsletters or other documents.
Template: template-doc-archive
Priority: high
Order: 20
*/

piklist('field', array(
	'type' => 'group',
	'field' => 'newsletter_archive',
	'label' => 'Document Archives',
	'description' => 'Each document includes an image, title and brief description.',
	'add_more' => true,
	'template' => 'field',
	'columns' => 12,
	'fields' => array(
		array(
			'type' => 'text',
			'field' => 'newsletter_archive_title',
			'label' => 'Title or issue date',
			'columns' => 12,
		),
		array(
			'type' => 'editor',
			'field' => 'newsletter_highlights',
			'label' => __( 'Brief description or highlights' ),
			'columns' => 12,
			'options' => array (
				'media_buttons' => false,
				'teeny' => true,
			),
		),
		array(
			'type' => 'file',
			'field' => 'newsletter_file',
			'label' => 'Upload or choose a PDF',
			'options' => array(
				'modal_title' => 'Add PDF File',
				'button' => 'Add PDF',
			),
		),
		array(
			'type' => 'file',
			'field' => 'newsletter_image',
			'label' => 'Add a cover image',
			'options' => array(
				'modal_title' => 'Add Image',
				'button' => 'Add Image',
			),
		),
	),
));