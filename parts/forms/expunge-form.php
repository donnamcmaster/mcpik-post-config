<?php
/*  
Title: Reports
Method: post
Message: Expunge Reservations
*/

/**
 *	The shortcode for this form is:
 *	[piklist_form form="expunge-form" add_on="mcw-forms"]
 */

piklist('field', array(
	'type' => 'text',
	'field' => 'expunge_year',
	'label' => 'Year to expunge:',
	'value' => date('Y')-1,
));

// Submit button
piklist('field', array(
	'type' => 'submit',
	'field' => 'submit',
	'value' => 'Expunge Reservations',
	'attributes' => array(
		'class' => 'btn btn-primary',
	),
));
