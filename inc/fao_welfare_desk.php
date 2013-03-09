<?php

/*
	To be included in client_det.php

	Assumes these variables:
	$client int (client id)
	$obj_client LcmClient object
	
*/

require 'FAOWelfareDesk.class.php';
require 'DataModification.class.php';

// only display anything if the client is currently supported
if ($obj_client->isCurrentlySupported()) {

	// If the FAO Welfare Desk form has been submitted,
	// save the data and display it
	if (isset($_POST['fao_amount'])) {
		$amount = (int)$_POST['fao_amount'];
		$bus_pass = isset($_POST['fao_bus_pass']) && $_POST['fao_bus_pass'] === 'on';
		$letter = isset($_POST['fao_letter']) && $_POST['fao_letter'] === 'on';
		$advocacy = isset($_POST['fao_advocacy']) && $_POST['fao_advocacy'] === 'on';
		$from_helpdesk = isset($_POST['fao_from_helpdesk']) && $_POST['fao_from_helpdesk'] === 'on';
		$note = mysql_real_escape_string($_POST['fao_note']);
		$faoWelfareDesk = new FaoWelfareDesk(
			$client, $amount, $bus_pass, $letter, $advocacy, $from_helpdesk, $note
		);
		DataModification::saveFaoWelfareDesk($faoWelfareDesk);

	// Otherwise, display data from either existing FAO Welfare Desk
	// object if any, or just with their usual support data.
	} else {
		$faoWelfareDesk = $obj_client->getFaoWelfareDesk();
	}
	require 'templates/fao_welfare_desk.tpl'; // will display $faoWelfareDesk
}
