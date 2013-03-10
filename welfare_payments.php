<?php

// universally required (auth, etc) 
require 'inc/inc.php';

// required for getting information to display
require 'inc/DataRetrieval.class.php';

// header, sidebar etc.
lcm_page_start();
// heading for individual page
matt_page_start('Welfare Payments');

// Display title and tabs
$tab = htmlentities($_GET['tab']);
require 'inc/templates/welfare_payments_header.tpl';

// Decide what to do based on tab chosen.
if ($tab === 'record') {
	show_input_form();
} else {
	show_printable_sheet();
}
	
function show_input_form() {
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
}

function show_printable_sheet() {
	// get client name, usual support and FAO Welfare Desk information
	// for all supported clients
	$data = DataRetrieval::getWelfareSheetInformation();
	require 'inc/WelfareSheetRow.class.php';
	require 'inc/SupportCombo.class.php';
	require 'inc/FAOWelfareDesk.class.php';
	$rows = WelfareSheetRow::createMany($data);
	//echo "<pre>"; print_r($rows); echo "</pre>";
	require 'inc/templates/welfare_payments_sheet.tpl';
}
