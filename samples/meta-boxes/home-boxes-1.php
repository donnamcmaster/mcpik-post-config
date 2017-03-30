<?php
/*
Title: First Row of Features
Post Type: page
Description: First row of feature boxes on the home page.
ID: 14
Priority: high
Order: 10
*/

piklist( 'field', array(
	'type' => 'group'
	,'field' => 'home_row1'
	,'label' => 'Description (When and Where)'
	,'description' => 'Please fill in exactly two boxes. Keep the content brief, and do not center the text.'
	,'columns' => 12
	,'add_more' => true
	,'fields' => array(
		array(
			'type' => 'text'
			,'field' => 'headline'
			,'label' => 'Headline'
			,'attributes' => array(
				'class' => 'large-text'
			)
		)
		,array(
			'type' => 'editor'
			,'field' => 'content'
			,'label' => 'Content'
			,'attributes' => array(
				'rows' => 5
			)
			,'options' => array (
				'media_buttons' => false
				,'drag_drop_upload' => false
			)
			,'validate' => array(
				array(
					'type' => 'limit'
					,'options' => array(
						'min' => 0
						,'max' => 25
						,'count' => 'words'
					)
				)
			)
		)
	)
));
