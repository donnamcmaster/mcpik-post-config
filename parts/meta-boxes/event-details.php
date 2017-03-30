<?php
/*
Title: Event Details
Post Type: event
Description: 
Priority: high
*/

$handler = McPik_Post_Type::get_handler( 'event' );
$handler->piklist_meta_box_fields( 'details' );