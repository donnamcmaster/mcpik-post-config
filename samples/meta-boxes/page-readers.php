<?php
/*
Title: Readers
Post Type: page
Description: Reader bios.
Template: reader-page-template
Priority: high
*/

piklist( 'field', array(
	'type' => 'group'
	,'field' => 'reader_bios'
	,'label' => __( 'Feature Boxes')
	,'columns' => 12
	,'add_more' => true
	,'fields' => array(
		array(
			'type' => 'text'
			,'field' => 'name'
			,'label' => 'Reader Name'
			,'attributes' => array(
				'class' => 'large-text'
			)
		)
		,array(
			'type' => 'text'
			,'field' => 'honor'
			,'label' => 'Reader Honor'
			,'help' => 'e.g., Oregon Poet Laureate'
			,'attributes' => array(
				'class' => 'large-text'
			)
		)
		,array(
			'type' => 'text'
			,'field' => 'url'
			,'label' => 'Reader Link'
			,'help' => 'make sure this is a "clickable" link'
			,'value' => 'http://'
			,'attributes' => array(
				'class' => 'large-text'
			)
		)
		,array(
			'type' => 'text'
			,'field' => 'link_text'
			,'label' => 'Reader Link Text'
			,'help' => 'text to display for the link, e.g., "susan.com" or "author website"'
			,'attributes' => array(
				'class' => 'large-text'
			)
		)
		,array(
			'type' => 'editor'
			,'field' => 'content'
			,'label' => 'Brief Bio'
			,'help' => 'not to exceed 100 words'
			,'attributes' => array(
				'class' => 'large-text'
				,'rows' => 5
			)
			,'options' => array (
				'media_buttons' => false
				,'teeny' => true
				,'textarea_rows' => 5
				,'drag_drop_upload' => false
			)
		)
		,array(
			'type' => 'file'
			,'field' => 'feature_image'
			,'label' => 'Image of the Reader'
			,'help' => 'Should be roughly square, and at least 114px in both dimensions.'
			,'options' => array(
				'modal_title' => 'Add Image'
				,'button' => 'Add Image'
			)
		)
	)
));
