<?php
/*
Title: Sidebar Map Layers
Post Type: page
Template: template-project-type
Description: parameters for a thumbnail map in the project type sidebar
Priority: high
*/

global $map_layers;

piklist('field', array(
	'type' => 'checkbox',
	'field' => 'ptype_map_layers',
	'label' => 'Layers to show by default',
	'attributes' => array(
		'class' => 'text',
	),
	'choices' => $map_layers,
));