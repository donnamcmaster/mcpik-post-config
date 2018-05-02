<?php
/*
Title: Error Messages
Setting: mcpk_reservation_settings
Order: 20
 */

piklist('field', array(
	'type' => 'editor',
	'field' => 'race_instructions',
	'label' => 'Instructions in the case where seats got sold while someone was paying',
	'description' => 'We will automatically substitute for %%NAME%%, %%SEATS_REQUESTED%%, %%SEATS_AVAILABLE%%, %%RESERVATION_ID%%.',
	'options' => array(
		'teeny' => 'true'
	),
));
