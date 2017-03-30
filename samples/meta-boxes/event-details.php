<?php
/*
Title: Event Details
Description: this is the description
Post Type: event
Priority: high
Order: 10
*/

piklist('field', array(
	'type' => 'datepicker',
	'field' => 'event_start_date',
	'label' => 'Start Date',
	'description' => 'Use the calendar, or enter like this: "2014-04-31" (year-month-day)',
	'options' => array(
		'dateFormat' => 'yy-mm-dd',
	),
	'attributes' => array(
		'size' => 12,
	), 
));
?>
