<?php
/*
Title: Sidebar Map Parameters
Post Type: page
Template: template-sub-basin
Description: defines the center point for the thumbnail map in the sidebar
Priority: high
*/

piklist('field', array(
	'type' => 'text',
	'field' => 'project_map_lat',
	'label' => 'Latitude',
));

piklist('field', array(
	'type' => 'text',
	'field' => 'project_map_lng',
	'label' => 'Longitude',
));