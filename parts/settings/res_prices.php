<?php
/*
Title: Shuttle Prices
Setting: mcpk_reservation_settings
Tab: Prices
Tab Order: 20
Order: 20
 */

piklist('field', array(
	'type' => 'text',
	'field' => 'rider_cost_std',
	'label' => 'Per-rider cost for standard run',
));

piklist('field', array(
	'type' => 'text',
	'field' => 'rider_cost_full',
	'label' => 'Per-rider cost for full-river run',
));

piklist('field', array(
	'type' => 'text',
	'field' => 'rider_cost_slab',
	'label' => 'Per-rider cost for Slab Creek or Ice House',
));
