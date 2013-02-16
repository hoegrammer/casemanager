<?php

/*
	This file is part of the Legal Case Management System (LCM).
	(C) 2004-2006 Free Software Foundation, Inc.

	This program is free software; you can redistribute it and/or modify it
	under the terms of the GNU General Public License as published by the
	Free Software Foundation; either version 2 of the License, or (at your
	option) any later version.

	This program is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
	or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
	for more details.

	You should have received a copy of the GNU General Public License along
	with this program; if not, write to the Free Software Foundation, Inc.,
	59 Temple Place, Suite 330, Boston, MA  02111-1307, USA

	$Id: add_client.php,v 1.8 2006/08/22 21:13:16 mlutfy Exp $
*/

include('inc/inc.php');
include_lcm('inc_acc');

$case = intval(_request('case'));
$_SESSION['errors'] = array();
//print_r($_SESSION);
if ($_SESSION['form_data']['id_followup'])
	{
	$destination = "edit_fu.php?special=yes&followup=". $_SESSION['form_data']['id_followup'];
	}
else
	{
	$destination = "case_det.php?case=$case";
	}
if (_request('ref_sel_client'))
	{
	$destination = _request('ref_sel_client');
	}

// Test access rights (unlikely to happen, unless hack attempt)
if (! ($case && allowed($case, 'a'))) {
	$_SESSION['errors']['generic'] = "Access denied"; // TRAD
	header("Location: " . $destination);
	exit;
}

// Add client to case
if (isset($_REQUEST['clients'])) {
	foreach ($_REQUEST['clients'] as $key=>$value) 
		$clients[$key] = intval($value);

	if ($clients) {
		foreach($clients as $client) {
			$q="INSERT INTO lcm_case_client_org
				SET id_case=$case,id_client=$client";

			$result = lcm_query($q);
		}
	}
//MATT WAS HERE. BIZARELY, HERE GOES THE CODE TO UPDATE A CLIENTS "LAST_WORK" FIELD WHEN A CASE IS CREATED
$matt = "UPDATE lcm_client as cl LEFT JOIN lcm_case_client_org as cco ON cl.id_client = cco.id_client SET cl.last_work = NOW() WHERE cco.id_case = ".$case;
lcm_query($matt);
}
// Add organisation to case
if (isset($_REQUEST['orgs'])) {
	foreach ($_REQUEST['orgs'] as $key => $value) 
		$orgs[$key] = intval($value);
	
	if ($orgs) {
		foreach($orgs as $org) {
			$q = "INSERT INTO lcm_case_client_org
					SET id_case = $case,
						id_org = $org";
			lcm_query($q);
		}
	}
}

// Remove client from case
if (isset($_REQUEST['id_del_client'])) {
	foreach ($_REQUEST['id_del_client'] as $id_client) {
		$q="DELETE FROM lcm_case_client_org
			WHERE id_case = $case
			AND id_client = $id_client";

		$result = lcm_query($q);
	}
}

// Remove organisation from case
if (isset($_REQUEST['id_del_org'])) {
	foreach ($_REQUEST['id_del_org'] as $id_org) {
		$q="DELETE FROM lcm_case_client_org
			WHERE id_case = $case
			AND id_org = $id_org";

		$result = lcm_query($q);
	}
}

lcm_header("Location: " . $destination . "#clients");

?>
