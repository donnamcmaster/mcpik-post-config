<?php
/**
 *	Class McPik_Post_Type_Event
 *
 *	Define specific shuttle event for weekends or weekdays. 
 *
 *	@package McPik Post Types
 *	@since McPik Post Types 1.0
 */

Class McPik_Post_Type_Event extends McPik_Post_Type {

function __construct ( ) {

	parent::init_post_type( 'event' );

	$this->post_fields['calendar'] = array(
		'scope' => 'taxonomy',
	);
	$this->post_fields['event_date_string'] = array(
		'scope' => 'post_meta',
		'type' => 'text',
	);
	$this->post_fields['event_start_date'] = array(
		'scope' => 'post_meta',
		'type' => 'text',
	);
	$this->post_fields['_mcw_event_url'] = array(
		'scope' => 'post_meta',
		'type' => 'text',
	);
	$this->post_fields['_mcw_learn_more_url'] = array(
		'scope' => 'post_meta',
		'type' => 'text',
	);
	$this->post_fields['_mcw_buy_tickets_url'] = array(
		'scope' => 'post_meta',
		'type' => 'text',
	);
	$this->post_fields['_mcw_event_email'] = array(
		'scope' => 'post_meta',
		'type' => 'text',
	);
	$this->post_fields['_mcw_event_phone'] = array(
		'scope' => 'post_meta',
		'type' => 'text',
	);
	$this->post_fields['thumb']['img_size'] = 'news-event-thumb';
}


/**
 *	Admin Methods
 */

public function manage_columns ( $defaults ) {
    unset( $defaults['date'] );
    unset( $defaults['author'] );
    $defaults['event_date_string'] = __( 'Event Date' );
    $defaults['event_start_date'] = __( 'Sort Date' );
    $defaults['_mcw_event_url'] = __( 'Event Website' );
    $defaults['_mcw_learn_more_url'] = __( 'Learn More' );
    $defaults['_mcw_buy_tickets_url'] = __( 'Buy Tickets' );
    $defaults['_mcw_event_email'] = __( 'Email' );
    $defaults['_mcw_event_phone'] = __( 'Phone' );
    $defaults['calendar'] = __( 'Calendars' );
    $defaults['thumb'] = __( 'Image' );
    return $defaults;
}

} // class
?>