<?php
/**
 *	Class McPik_Post_Type_product
 *
 *	Custom functions for WooCommerce products that represent shuttle routes. 
 *
 *	@package McPik Post Types
 *	@since McPik Post Types 1.0
 */

define( 'RES_MANIFEST_ROWS', 14 );

Class McPik_Post_Type_product extends McPik_Post_Type {

function __construct ( ) {
	$this->registered = true;
	parent::init_post_type( 'product' );

	$this->post_fields['cut_off_time'] = array(
		'scope' => 'post_meta',
		'type' => 'text',
		'col_width' => 'small',
	);
}


/**
 *	Database Lookup & Display Helpers
 */
public static function route_name ( $shuttle_ID ) {	// REWRITE?
	$route_ID = get_post_meta( $shuttle_ID, 'shuttle_route', true );
	$route = get_post( $route_ID );
	return $route->post_title;
}

public static function route_info ( $shuttle_ID ) {	// REWRITE?
	$route_ID = get_post_meta( $shuttle_ID, 'shuttle_route', true );
	$route = get_post( $route_ID );
//	return wpautop( $route->post_content );
	return nl2br( $route->post_content );
}

public static function open_seats( $shuttle_ID ) {
	return get_post_meta( $shuttle_ID, '_stock', true );
}


/**
 *	order_shuttles_by_route
 *	- given array of shuttles (combo of route & date), reorders them by route order
 *	- route order = menu_order of the route product
 */
private static function order_shuttles_by_route ( $route_info ) {
	$args = array(
		'post_type' => 'product',
		'posts_per_page' => -1,
		'fields' => ['ID','menu_order'],
	);
	$routes = get_posts( $args );

	$sorted = [];
	foreach ( $routes as $route ) {
		foreach ( $route_info as $shuttle ) {
			if ( $shuttle['product_ID'] == $route->ID ) {
				$sorted[] = $shuttle;
				break;
			}
		};
	}
	return $sorted;
}


/**
 *	find_routes_for_date
 *	- given a date, returns an array of routes running on that date
 *	- in WooCommerce terms, this translates to an array of products 
 	  that contain a variation for that date
 *	- for each route, provides product ID, name, variation ID, and # of open seats
 *	- null means no routes run on that date
 *	- routes with no seats available are listed with open seats = 0
 */
public static function find_routes_for_date ( $timestamp, $order_by_route=false ) {

	$date = date( 'n/j/Y', $timestamp );
	$args = array(
		'post_type' => 'product_variation',
		'posts_per_page' => -1,
		'fields' => ['ID','post_parent'],
		'meta_query' => array(
			array(
				'key' => 'attribute_date',
				'value' => date( 'n/j/Y', $timestamp ),
				'compare' => '=',
			),
		),
	);
	$shuttles = get_posts( $args );
	if ( !$shuttles ) {
		$route_info = null;

	} else {
		$route_info = [];
		foreach ( $shuttles as $shuttle ) {
			// post_parent is the product (route) ID
			$route = get_post( $shuttle->post_parent );
			if ( !$route ) {
				mcw_log( "no route post for {$shuttle->post_parent}" );
				continue;
			}
			if ( $route->post_status == 'publish' ) {
				$route_info[] = array(
					'product_ID' => $shuttle->post_parent,
					'route_name' => $route->post_title,
					'product_slug' => $route->post_name,
					'date' => $date,
					'variation_ID' => $shuttle->ID,
					'open_seats' => self::open_seats( $shuttle->ID ),
				);
			}
		} // foreach
	} // else

	if ( $route_info && $order_by_route ) {
		return self::order_shuttles_by_route( $route_info );
	} else {
		return $route_info;
	}
}


/**
 *	report_availability
 *	- given a date, prints a list of routes running on that date, with # of seats
 *	- routes with no seats available are listed as "SOLD OUT"
 *	- if no routes running on that date, returns null & prints nothing
 */
public static function report_availability ( $timestamp, $print_headline=true ) {
	$shuttles = self::find_routes_for_date( $timestamp, true );
	if ( !$shuttles ) {
		return null;
	}

	if ( $print_headline ) {
?>
	<h2><?= date( 'l, F j, Y', $timestamp ); ?></h2>
<?php
	}
?>
	<p>
<?php
	foreach ( $shuttles as $shuttle ) {
		extract( $shuttle );
		$availability = $open_seats ? "$open_seats seats open" : 'SOLD OUT';
		$date_for_url = urlencode( $date );
		$route_link = "/product/$product_slug/?date=$date_for_url";
?>
	<a href="<?= $route_link;?>"><?= $route_name;?></a><?= " &ndash; $availability"?><br>

<?php
	}
?>
	</p>
<?php
}


public static function print_availability_forecast ( $nr_weeks ) {
	// create date range
	$current = time();
	$endpoint = strtotime( "+$nr_weeks weeks" );
	while ( $current <= $endpoint ) {
		self::report_availability( $current );
		$current = strtotime( '+1 day', $current );
	}
}


/**
 *	Print Manifest
 */
static function get_integer ( $string ) {
	$integer = (int)$string;
	return $integer ? $integer : '-';
}


/**
 *	Manifest: find_orders_for_variation
 *	props LoicTheAztec https://stackoverflow.com/questions/42527147/get-woocommerce-orders-items-by-variation-id

 *	intermediate information: 
 *	$results is an array of items, where each item is like: 
	[0] => Array (
		[order_id] => 14878
		[item_id] => 7
	)
 */
private static function find_orders_items_for_variation ( $variation_id ) {

	global $wpdb;

	// get all order IDs and item ids with that variation ID
	$results = $wpdb->get_results( $wpdb->prepare( "
		SELECT items.order_id, items.order_item_id AS item_id
		FROM {$wpdb->prefix}woocommerce_order_items AS items
		LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS itemmeta ON items.order_item_id = itemmeta.order_item_id
		WHERE meta_key LIKE '_variation_id'
		AND meta_value = %s
		", $variation_id ), ARRAY_A );

	if ( !$results ) {
		return false;
	}

	$items_detail = [];

	foreach ( $results as $item ) {
		extract( $item );	// $item_id and $order_id

		$order = new WC_Order( $item['order_id'] );
		$first_name = $order->get_billing_first_name();
 		$last_name = $order->get_billing_last_name();
		
		$items_detail[$item_id] = array(
			'order_id' => $item['order_id'],
			'order_name' => $first_name.' '.$last_name,
		);
piklist::pre( $items_detail );

		// get all item meta for this item
		$item_meta = $wpdb->get_results( $wpdb->prepare( "
			SELECT `meta_key`,`meta_value` 
			FROM {$wpdb->prefix}woocommerce_order_itemmeta 
			WHERE order_item_id = %s
		", $item['item_id'] ), ARRAY_A );

piklist::pre( $item_meta );

		foreach ( $item_meta as $meta ) {
			switch ( $meta['meta_key'] ) {
				case '_qty':
					$items_detail[$item_id]['riders'] = $meta['meta_value'];
					break;
				case '_line_total':
					$items_detail[$item_id]['price'] = $meta['meta_value'];
					break;
				case 'drop_off_pickup_location':
					$items_detail[$item_id]['shuttle_stop'] = $meta['meta_value'];
					break;
				case 'special_request':
					$items_detail[$item_id]['special_request'] = $meta['meta_value'];
					break;
				case 'bringing_gear':
					$items_detail[$item_id]['bringing_gear'] = $meta['meta_value'];
					break;
				case 'oar_boats':
					$items_detail[$item_id]['oar_boats'] = $meta['meta_value'];
					break;
				case 'rafts':
					$items_detail[$item_id]['rafts'] = $meta['meta_value'];
					break;
				case 'kayaks':
					$items_detail[$item_id]['kayaks'] = $meta['meta_value'];
					break;
				case 'ikayaks':
					$items_detail[$item_id]['ikayaks'] = $meta['meta_value'];
					break;
				case 'bikes':
					$items_detail[$item_id]['bikes'] = $meta['meta_value'];
					break;
				case 'driver_note':
					$items_detail[$item_id]['driver_note'] = $meta['meta_value'];
					break;
				default:
					// do nothing				
			}
		}

	}

	return $items_detail;
}


/*
print_shuttle_manifest
	$shuttle = array(
		'product_ID' => $shuttle->post_parent,
		'route_name' => $route->post_title,
		'product_slug' => $route->post_name,
		'date' => $date,
		'variation_ID' => $shuttle->ID,
		'open_seats' => self::open_seats( $shuttle->ID ),
	);
*/
private static function print_shuttle_manifest ( $shuttle ) {
	$items_detail = self::find_orders_items_for_variation( $shuttle['variation_ID'] );

	$total_riders = 0;
	
	// we always print a table with RES_MANIFEST_ROWS (14) rows, regardless of whether there are reservations
?>
<table class="manifest table table-striped table-bordered table-condensed" cellspacing="0" cellpadding="0">
<thead>
	<tr>
		<th class="medium">Name</th>
		<th class="tiny">&#x2713;</th>
		<th class="tiny">Riders</th>
		<th class="tiny">OB</th>
		<th class="tiny">PR</th>
		<th class="tiny">K</th>
		<th class="tiny">IK</th>
		<th class="tiny">BK</th>
		<th class="tiny">Stop</th>
		<th class="tiny">$$</th>
		<th class="wide">Notes</th>
	</tr>
</thead>
<tbody>
<?php
	$stops = get_terms( 'shuttle_stops' );
piklist::pre( $stops );

	$nr_rows = 0;
	foreach ( $stops as $stop ) {
		if ( $items_detail ) {
			foreach ( $items_detail as $item ) {
				$total_riders += $item['riders'];

				$oar_boats = isset( $item['oar_boats'] ) ? self::get_integer( $item['oar_boats'] ) : '-';
				$paddle_rafts = isset( $item['rafts'] ) ? self::get_integer( $item['rafts'] ) : '-';
				$kayaks = isset( $item['kayaks'] ) ? self::get_integer( $item['kayaks'] ) : '-';
				$ikayaks = isset( $item['ikayaks'] ) ? self::get_integer( $item['ikayaks'] ) : '-';
				$bikes = isset( $item['bikes'] ) ? self::get_integer( $item['bikes'] ) : '-';
				$shuttle_stop = isset( $item['shuttle_stop'] ) ? $item['shuttle_stop'] : '-';
				$nr_rows++;
?>
	<tr>
		<td><?php echo $item['order_name'];?></td>
		<td>&nbsp;</td>
		<td class="number"><?php echo self::get_integer( $item['riders'] );?></td>
		<td class="number"><?php echo $oar_boats;?></td>
		<td class="number"><?php echo $paddle_rafts;?></td>
		<td class="number"><?php echo $kayaks;?></td>
		<td class="number"><?php echo $ikayaks;?></td>
		<td class="number"><?php echo $bikes;?></td>
		<td style="white-space:nowrap;"><?php echo $shuttle_stop;?></td>
		<td class="number"><?php echo $item['price'];?></td>
		<td><?php if ( isset( $item['driver_note'] ) ) echo $item['driver_note'];?></td>
	</tr>
<?php
			}
		}
	}
	if ( $nr_rows < RES_MANIFEST_ROWS ) {
		for ( $i=$nr_rows; $i<RES_MANIFEST_ROWS; $i++ ) {
?>
	<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
<?php
		}
	}
?>
</tbody>
</table>
	<p class="summary"><strong>Total Riders with Reservations</strong>: <?= $total_riders;?></p>
<?php
}

public static function print_manifest ( $date, $pretty_date ) {
	$timestamp = strtotime( $date );
	$shuttles = self::find_routes_for_date( $timestamp, true );

	if ( !$shuttles ) {
?>
	<p>No shuttles for <?= $pretty_date;?>.</p>
<?php
		return;
	}

	// we get here if we have shuttles
	$printed_on_page = 0;
	foreach ( $shuttles	as $shuttle ) {
		if ( ++$printed_on_page > 2 ) {
			$printed_on_page = 1;
?>
<div class="page-breaker"></div>
<?php
		}
?>
<h2><?= $pretty_date; ?> &raquo;&nbsp;&nbsp;<?= $shuttle['route_name']; ?></h2>
<?php
		self::print_shuttle_manifest( $shuttle );
	}
} // print_manifest


/**
 *	Display Information
 *	- functions to print current routes and availability information
 */
function print_routes ( $context='page' ) {	// REWRITE?
	// get a list of routes
	$args = array(
		'post_type' => 'route',
		'numberposts' => 99,
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'route_status' => 'public',
	);
	$routes = get_posts( $args );
	foreach ( $routes as $route ) {
		$route_number = get_post_meta( $route->ID, 'route_number', true );
		mcw_print_headline( 3, $route_number . '. '. $route->post_title );
		echo wptexturize( wpautop( $route->post_content ) );
	}
}


/**
 *	Admin Methods
 */

public function admin_notices () {
    global $pagenow;
}

public function manage_columns ( $defaults ) {
	$defaults['cut_off_time'] = __( 'Cut-off' );
    return $defaults;
}

} // class