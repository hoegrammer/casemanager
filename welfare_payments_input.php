<?php

// universally required (auth, etc) 
require 'inc/inc.php';
// header, sidebar etc.
lcm_page_start();
// heading for individual page
matt_page_start('Welfare Payments Input Form', 'sub');


require 'inc/DataModification.class.php';
require 'inc/DataRetrieval.class.php';
require 'inc/WelfarePayment.class.php';


if ($_POST) {
	$date = date('Y-m-d');
	for($i = 0; $i < sizeof($_POST['id_case']); $i ++) {
		$id_case  = $_POST['id_case'][$i];
		$amount   = (int)$_POST['amount'][$id_case];
		$bus_pass = (int)$_POST['bus_pass'][$id_case] === 'on' ? 1 : 0;
		$note   = $_POST['note'][$id_case];
		$welfare_payment = new WelfarePayment($id_case, $amount, $bus_pass, $note);
		DataModification::saveWelfarePayment($welfare_payment);
	}
} else {
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
