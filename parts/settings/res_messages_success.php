<?php
/*
Title: Success Messages
Setting: mcpk_reservation_settings
Order: 10
 */

piklist('field', array(
	'type' => 'editor',
	'field' => 'success_confirm',
	'label' => 'Success Confirmation',
	'description' => 'We will automatically substitute for %%NAME%%, %%SEATS_REQUESTED%%, %%SEATS_AVAILABLE%%, %%RESERVATION_ID%%.',
	'options' => array(
		'teeny' => 'true'
	),
));

