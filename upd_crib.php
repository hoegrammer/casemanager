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

	$Id: upd_fu.php,v 1.57 2006/11/22 23:37:06 mlutfy Exp $
*/

include('inc/inc.php');
include_lcm('inc_acc');
include_lcm('inc_filters');
include_lcm('inc_obj_fu');


// Clear all previous errors
$_SESSION['errors'] = array();

$id_crib = intval(_request('id_crib', 0));
$test= _request('short');

// Get form data from POST fields
foreach($_POST as $key => $value)
    $_SESSION['form_data'][$key]=$value;


//print "HELLO";
//print intval(_request('visible'));
//exit;

// Get old FU data, if updating
$old_crib_data = array();
if ($id_crib) {
	$q = "SELECT *
			FROM lcm_crib
			WHERE id_crib = $id_crib";

	$result = lcm_query($q);

	if (! ($old_crib_data = lcm_fetch_array($result)))
		lcm_panic("Could not find requested crib!");
}

//
// Check if any errors found
//
if (count($_SESSION['errors'])) {
    lcm_header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

$qz = 	"full = '" . clean_input(_request('content')) . "',
	 short = '"   . clean_input(_request('short'))   . "',
	 visible = '" . clean_input(_request('visible')) . "',
	 id_keyword = '" . clean_input(_request('casetype')) . "'";

if ($id_crib > 0 )
	{
	$q = "UPDATE lcm_crib SET date_created = NOW(), $qz WHERE id_crib = " . $id_crib ;
	}
else
	{
	$q = "INSERT INTO lcm_crib SET date_created = NOW(), $qz";
	}
$result= lcm_query($q);


//if (count ($errs))
//	$_SESSION['errors'] = array_merge($_SESSION['errors'], $errs);
//
//if (count($_SESSION['errors'])) {
//    lcm_header("Location: " . $_SERVER['HTTP_REFERER']);
//    exit;
//}

//if (! $id_followup)
//	$id_followup = $fu->getDataInt('id_followup', '__ASSERT__');

//
// Update lcm_case.date_update (if fu.date_start > c.date_update)
//
		
// Send user back to add/edit page's referer or (default) to followup detail page
lcm_header('Location: cribnotes.php');

exit;

?>
