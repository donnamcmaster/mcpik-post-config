<?php
/*
Title: Image Details
Description: 
Post Type: attachment
Priority: high
Order: 10
*/
	
piklist('field', array(
	'type' => 'text',
	'scope' => 'post_meta',
	'field' => 'image_credit',
	'label' => 'Image Credit',
	'attributes' => array(
		'size' => 36,
	),
));

piklist('field', array(
	'type' => 'datepicker',
	'field' => 'image_create_date',
	'label' => 'Date Image Created',
	'description' => 'Use the calendar, or enter like this: "4/31/2013" (month/day/year)',
	'options' => array(
		'dateFormat' => 'm/d/yy',
	),
	'attributes' => array(
		'size' => 12,
	),
));
?>
