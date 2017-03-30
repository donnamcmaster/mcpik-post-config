<?php
/*
Title: Banner Ad Details
Post Type: ad
Description: 
Priority: high
*/

$handler = McPik_Post_Type::get_handler( 'ad' );
$handler->piklist_meta_box_fields( 'details' );