<?php

/*
	To be included in client_det.php

	Assumes these variables:
	$client int (client id)
	$obj_client LcmClient object
	
*/



// only display anything if the client is currently supported

	// If the FAO Welfare Desk form has been submitted,
// save the data and display it
if (isset($_POST['accompanied_by'])) {
	DataModification::saveAccompaniedBy($_POST['accompanied_by'], $obj_client->getId());
	$accompanied_by = $_POST['accompanied_by'];
} else {
	$accompanied_by = $obj_client->getAccompaniedBy();
}
require 'templates/accompanied_by.tpl'; // will display $faoWelfareDesk
