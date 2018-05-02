<?php
/*
Title: Sponsor Details
Post Type: sponsor
Description: 
Priority: high
*/

$handler = McPik_Post_Type::get_handler( 'sponsor' );
$handler->piklist_meta_box_fields( 'details' );