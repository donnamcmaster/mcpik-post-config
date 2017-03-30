<?php
/*
Title: Listing Details
Post Type: listing
Description: 
Priority: high
*/

$handler = McPik_Post_Type::get_handler( 'listing' );
$handler->piklist_meta_box_fields( 'details' );