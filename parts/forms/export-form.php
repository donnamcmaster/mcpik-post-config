<?php
/*  
Title: Reports
Method: post
Message: Export Reservations
*/

/**
 *	The shortcode for this form is:
 *	[piklist_form form="export-form" add_on="mcpik-post-types"]
 */

piklist('field', array(
	'type' => 'text',
	'field' => 'export_year',
	'label' => 'Year to export:',
	'value' => date( 'Y' )-1, // prior year
));

// Submit button
piklist('field', array(
	'type' => 'submit',
	'field' => 'submit',
	'value' => 'Export Reservations',
	'attributes' => array(
		'class' => 'btn btn-primary',
	),
));
