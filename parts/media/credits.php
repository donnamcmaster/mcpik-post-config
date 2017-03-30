<?php
/*
Title: Photo Credit
Post Type: attachment
Description: 
Priority: high
*/

$handler = McPik_Post_Type::get_handler( 'attachment' );
$handler->piklist_meta_box_fields( 'credits' );