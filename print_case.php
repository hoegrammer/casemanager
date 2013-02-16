<?php

/*
	This file is part of the Legal Case Management System (LCM).
	(C) 2004-2005 Free Software Foundation, Inc.

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

	$Id: case_det.php,v 1.177 2006/09/07 19:51:44 mlutfy Exp $
*/

// MATT WAS HERE. THIS FILE (PRINT_CASE) IS A SIMPLIFIED VERSION OF CASE_DET FOR PRINTING/
include('inc/inc.php');
include_lcm('inc_acc');
include_lcm('inc_filters');
include_lcm('inc_obj_case');

// Read parameters
$case = intval(_request('case'));
$fu_order = "ASC";

// Read site configuration settings
$case_assignment_date = read_meta('case_assignment_date');
$case_alledged_crime  = read_meta('case_alledged_crime');
$case_legal_reason    = read_meta('case_legal_reason');
$case_allow_modif     = read_meta('case_allow_modif');
$modify = ($case_allow_modif == 'yes');

	$q="SELECT *
		FROM lcm_case
		WHERE id_case=$case";

	$result = lcm_query($q);

	// Process the output of the query
	if ($row = lcm_fetch_array($result)) {

		$add   = allowed($case,'w');
		$edit  = allowed($case,'e');
		$admin = allowed($case,'a');

		// Show case details
		$obj_case_ui = new LcmCaseInfoUI($row['id_case']);
		$obj_case_ui->printBasic();
		
		// Show case client(s)
		$q="SELECT cl.id_client, cl.name_first, cl.name_middle, cl.name_last
			FROM lcm_case_client_org as clo, lcm_client as cl
			WHERE id_case = $case AND clo.id_client = cl.id_client";
		
		$result = lcm_query($q);
		if (lcm_num_rows($result)) {
			while ($row = lcm_fetch_array($result)) {
				// name
				echo  '<li>Client: '.get_person_name($row).'</li>';
			}
		}
		print "</ul>";
		
		// Case followups
		$obj_case_ui = new LcmCaseInfoUI($case);
		$obj_case_ui->mattPrintFollowups(true);
	} else {
		echo "<p>No such case :(</p>\n";

	}

	$_SESSION['errors'] = array();
	$_SESSION['case_data'] = array(); // DEPRECATED
	$_SESSION['form_data'] = array();
	$_SESSION['fu_data'] = array();

?>
