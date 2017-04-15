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

	$this->register_args = array(
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'query_var' => true,
		'rewrite' => true,
		'has_archive' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'exclude_from_search' => false,
		'menu_icon' => 'dashicons-calendar-alt',
		'supports' => array(
			'title',
			'editor',
			'excerpt',
			'thumbnail',
			'revisions',
		),
		'hide_meta_box' => array(
			'revisions',
			'members-cp',
		),
	); 

	parent::init_post_type( 'event' );

	// define & register taxonomy for this post_type
	$tax_args = array(
		'hierarchical' => true,
		'label' => 'Calendar',
		'query_var' => true,
		'rewrite' => false,
	);
	register_taxonomy( 'calendar', 'event', $tax_args );
	$this->post_fields['calendar'] = array(
		'scope' => 'taxonomy',
	);

	$this->post_fields['_mcw_event_start_date'] = array(
		'scope' => 'post_meta',
		'label' => 'Start date for event (required): ',
		'help' => 'Enter dates as YYYY-MM-DD (e.g., 2012-07-22)',
		'col_title' => 'Start Date',
		'type' => 'date',
		'null_ok'	=>	false,
	);
	$this->post_fields['_mcw_event_end_date'] = array(
		'scope' => 'post_meta',
		'label' => 'End date for multi-day event: ',
		'col_title' => 'End Date',
		'type' => 'date',
	);


	$this->post_fields['event_date_string'] = array(
		'type' => 'text',
		'label' => 'Human-Friendly Date',
		'description' => 'E.g., October 13-16, 2016.',
		'attributes' => array(
			'class' => 'regular-text',
		),
	);

	$this->post_fields['event_start_date'] = array(
		'type' => 'datepicker',
		'label' => 'Computer Sortable Date',
		'description' => 'Use the calendar, or enter like this: "2016-12-15."',
		'options' => array(
			'dateFormat' => 'yy-mm-dd',
		),
	);

	$this->post_fields['_mcw_event_url'] = array(
		'type' => 'text',
		'scope' => 'post_meta',
		'label' => 'Event website: ',
		'description' => 'Enter web address, beginning with http://',
		'col_title' => 'URL',
		'attributes' => array(
			'class' => 'regular-text',
		),
	);
	$this->post_fields['_mcw_event_email'] = array(
		'type' => 'text',
		'scope' => 'post_meta',
		'label' => 'Contact email address: ',
		'col_title' => 'Email',
		'attributes' => array(
			'class' => 'regular-text',
		),
	);
	$this->post_fields['_mcw_event_phone'] = array(
		'type' => 'text',
		'scope' => 'post_meta',
		'label' => 'Contact phone number: ',
		'col_title' => 'Phone',
	);
	$this->post_fields['thumb']['img_size'] = 'event_img';


	$this->meta_boxes = array(
		'details' => array(
			'event_date_string',
			'event_start_date',
			'_mcw_event_url',
			'_mcw_event_email',
			'_mcw_event_phone',
		),
	);
}


/**
 *	Display Methods
 */

protected function init_filters_and_actions () {
	add_shortcode( 'mcw_events', array( $this, 'mcw_events' ) );
}


/**
 *	calendar events shortcode
 *	[mcw_events]	list all events
 *	[mcw_events calendar="3"]	list only events in calendar 3
 *	[mcw_events format="brief"] list only titles & dates (default="full")
 *	[mcw_events expire=true]	drop events after they are over? (default=false)
 *	(set union not supported)
 */
public function mcw_events ( $atts ) {
	extract( shortcode_atts( array(
		'calendar' => '',
		'format' => 'full',
		'expire' => false,
		'display_date' => true,
	), $atts ) );

	if ( $format == 'brief' ) {
		$count = 4;
	} else {
		$count = 99;
	}

	// initialize events query
	$args = array (
		'post_type' => 'event',
		'orderby' => 'meta_value',
		'meta_key' => '_mcw_event_start_date',
		'order' => 'ASC',
		'posts_per_page' => $count,
	);

	if ( $expire ) {
		// check freshness; keep events in the calendar for a week before cutting them off
		$args['meta_query'] = array (
			array(
				'key' => '_mcw_event_start_date',
				'value' => date( 'Y-m-d', strtotime( '-1 week' ) ),
				'compare' => '>'
			),
		);
	}
	if ( $calendar ) {
		$args['calendar'] = $calendar;
	}
	$separator = ( $format == 'brief' ) ? ' &ndash; ' : ' &mdash; ';

	// start loop
	$event_list = get_posts( $args );

	ob_start();
?>
<ul class="event-calendar">

<?php
	foreach ( $event_list as $event ) {
		$custom_fields = get_post_custom( $event->ID );
		$date_string = $display_date ? ' &mdash; '.McPik_Utils::get_custom_value( 'event_date_string', $custom_fields ) : '';

		if ( $format == 'brief' ) {
			$title = wp_texturize( $event->post_title );
?>
	<li><a href="/calendar/#event-<?= $event->ID;?>"><?= $title; ?></a><?= $date_string;?></li>

<?php

		} else {
			$thumb = has_post_thumbnail( $event->ID ) ? get_the_post_thumbnail( $event->ID, 'img-col-horz' ) : '';
			$url = McPik_Utils::get_custom_value( '_mcw_event_url', $custom_fields );
			$anchor = $url ? BR . McPik_Utils::get_anchor_blank( $url, 'event website' ) : '';
			$content = wpautop( wptexturize( $event->post_content . ' ' . $anchor ) );
?>
	<h2 id="event-<?= $event->ID;?>"><?= $event->post_title, $date_string, edit_post_link( '', '', '', $event->ID ); ?></h2>
	<li class="row panel_imgright">
		<div class="col-sm-7 box-text">
			<?= $content; ?>
		</div>
		<div class="col-sm-5 box-image">
			<?= $thumb; ?>
		</div>
	</li>
<?php
		}
	}
?>
</ul>

<?php
	return ob_get_clean();
}

/**
 *	Admin Methods
 */

public function manage_columns ( $defaults ) {
    unset( $defaults['date'] );
    unset( $defaults['author'] );
    $defaults['_mcw_event_start_date'] = __( 'Start' );
    $defaults['_mcw_event_end_date'] = __( 'End' );
    $defaults['_mcw_event_url'] = __( 'URL' );
    $defaults['calendar'] = __( 'Calendars' );
    $defaults['thumb'] = __( 'Image' );
    return $defaults;
}

} // class
?>