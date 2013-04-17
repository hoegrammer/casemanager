<?php

require  'inc/inc.php';
require 'inc/WelfarePayment.class.php';
require 'inc/DataModification.class.php';

// welfare input form submits to here.

// Post should contain records to be saved.

for($i = 0; $i < sizeof($_POST['id_case']); $i ++) {
	$id_case  = $_POST['id_case'][$i];

	// only save rows that have had something entered
	if ($_POST['amount'][$id_case] !== ''
		|| $_POST['bus_pass'][$id_case] === 'on'
		|| $_POST['note'][$id_case] !== ''
	) {
		$amount   = (int)$_POST['amount'][$id_case];
		$bus_pass = isset($_POST['bus_pass'][$id_case]) && $_POST['bus_pass'][$id_case] === 'on' ? 1 : 0;
		$note   = $_POST['note'][$id_case];
		$welfare_payment = new WelfarePayment($id_case, $amount, $bus_pass, $note);
		var_dump($welfare_payment);
		DataModification::saveWelfarePayment($welfare_payment);
	}
}

// Deals with the fact that casemanager might be on the doc root
// or might be in a directory below it.
$dir = dirname($_SERVER['PHP_SELF']);
if (strlen($dir > 1)) {
        header("Location: $dir/welfare_payments.php?tab=record", true, 303);
} else {
        header('Location: /welfare_payments.php?tab=record', true, 303);
}
~ 
