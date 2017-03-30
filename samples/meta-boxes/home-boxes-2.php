<?php
/*
Title: Second Row of Features
Post Type: page
Description: Second row of feature boxes on the home page.
ID: 14
Priority: high
Order: 20
*/


piklist( 'field', array(
	'type' => 'group'
	,'field' => 'home_row2'
	,'label' => 'Inviting Details'
	,'description' => 'Please fill in exactly three boxes. Keep the content brief, and do not center the text.'
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
				'class' => 'large-text'
				,'rows' => 5
			)
			,'options' => array (
				'media_buttons' => false
				,'teeny' => true
				,'textarea_rows' => 3
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
