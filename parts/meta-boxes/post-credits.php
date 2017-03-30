<?php
/*
Title: Credits
Description: Credit byline for news author, links to photographers.
Post Type: post,page
Priority: high
Order: 10
*/

$handler = McPik_Post_Type::get_handler( 'post' );
$handler->piklist_meta_box_fields( 'credits' );