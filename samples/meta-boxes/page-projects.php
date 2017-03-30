<?php
/*
Title: Related Projects
Post Type: page
Description: list of projects related to this page
*/
  
piklist('field', array(
	'type' => 'post-relate',
	'scope' => 'project',
	'template' => 'field',
	'field' => 'related_project',
	'label' => 'Related Projects',
));
