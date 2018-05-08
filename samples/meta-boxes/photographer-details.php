<?php
/*
Title: Photographer Details
Post Type: photographer
Description: 
Priority: high
*/

$handler = McPik_Post_Type::get_handler( 'photographer' );
$handler->piklist_meta_box_fields( 'details' );