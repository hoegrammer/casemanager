<?php

// universally required (auth, etc) 
require 'inc/inc.php';
// header, sidebar etc.
lcm_page_start();
// heading for individual page
matt_page_start('Welfare Payments Input Form', 'sub');


require 'inc/DataRetrieval.class.php';

// Get client names and ids for everyone who is currently financially supported
// and does not have a welfare payment update for today
$clients = DataRetrieval::getAllCurrentlySupportedClientsNotUpdatedToday();

// if there are none, say so
if (empty($clients)) {
	echo "All clients have already been updated.";
} else {
	// otherwise display the form
	require 'inc/templates/welfare_payments_input.tpl';
}
