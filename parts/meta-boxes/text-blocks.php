<?php
/*
Title: Text Blocks
Post Type: page
Priority: high
Order: 40
*/

piklist( 'field', array(
	'type' => 'group',
	'field' => 'text_blocks',
	'label' => 'Text Blocks',
	'description' => 'Blocks of text to use in shortcodes.',
	'columns' => 12,
	'template' => 'field',
	'add_more' => true,
	'fields' => array(
		array(
			'type' => 'text',
			'field' => 'block_id',
			'label' => 'Unique Name',
		),
		array(
			'type' => 'editor',
			'field' => 'block_text',
			'label' => 'Content',
			'options' => array (
				'media_buttons' => true,
				'teeny' => false,
			),
		),
	)
));
