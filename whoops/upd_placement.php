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

	$Id: upd_author.php,v 1.26 2006/08/17 14:05:53 mlutfy Exp $
*/

include('inc/inc.php');
include_lcm('inc_filters');

// Clear all previous errors
$_SESSION['errors'] = array();
$_SESSION['form_data'] = array();

// Get form data from POST fields
foreach($_GET as $key => $value)
    $_SESSION['form_data'][$key] = $value;
//
// Start SQL query
//
$fl = "date_update = NOW()";

if (($_SESSION['form_data']['when_year'] =='')||($_SESSION['form_data']['when_month'] =='')||($_SESSION['form_data']['when_day'] ==''))
	{
	$_SESSION['errors']['when']='No Date of Offer.';
	}
else
	{
	$when=$_SESSION['form_data']['when_year'].'-'.$_SESSION['form_data']['when_month'].'-'.$_SESSION['form_data']['when_day'];
	$fl.= ", date_start = '".$when."'";
	}

if ($_SESSION['form_data']['room'] =='')
	{
	$_SESSION['errors']['room']='You must select a Room!';
	}
else
	{
	$q = "SELECT * from lcm_placement where id_room = "._session('room')." 
			and status = 'active' 
			and 
				(
					(to_days(date_start) <= to_days('".$when."') and to_days(date_end) > to_days('".$when."'))
					or
					(to_days(date_start) > to_days('".$when."'))
				)";
	$result = lcm_query($q);
	if (lcm_fetch_array($result))
		{
		$_SESSION['errors']['room']='That room is not free on that date!';
		}
	else
		{
		$fl.= ", id_room = '"._session('room')."'";
		}
	}

$fl.= 'status=provisional';


if ($_SESSION['form_data']['room'] =='')
	{
	$_SESSION['errors']['room']='You must select a Room!';
	}

/*
// First name must have at least one character
if (strlen(lcm_utf8_decode(_session('name_first'))) < 1) {
	$_SESSION['errors']['name_first'] = _T('person_input_name_first') . ' ' . _T('warning_field_mandatory');
} else {
	$fl .= ", name_first = '" . _session('name_first')  . "'";
}

// Middle name can be empty
$fl .= ", name_middle = '" . _session('name_middle') . "'";

// Last name must have at least one character
if (! strlen(lcm_utf8_decode(_session('name_last')))) {
	$_SESSION['errors']['name_last'] = _T('person_input_name_last') . ' ' . _T('warning_field_mandatory');
} else {
	$fl .= ", name_last = '" . _session('name_last') . "'";
}


if ($author_session['status'] == 'admin')
	{
	$fl .= ", id_office = '" . _session('id_office') . "'";
	}

// Author status can only be changed by admins
if ($author_session['status'] == 'admin')
	$fl .= ", status = '" . _session('status') . "'";

if (_session('id_author') > 0) {
	$q = "UPDATE lcm_author 
			SET $fl 
			WHERE id_author = " . _session('id_author');
	$result = lcm_query($q);
} else {
	if (count($errors)) {
    	header("Location: edit_author.php?author=0");
		exit;
	}

	$q = "INSERT INTO lcm_author SET date_creation = NOW(), username = '', password = '', $fl";
	$result = lcm_query($q);
	$_SESSION['form_data']['id_author'] = lcm_insert_id('lcm_author', 'id_author');
	$_SESSION['form_data']['id_author'] = _session('id_author');
}

//
// Change password (if requested)
//

if (_session('usr_new_passwd') || (! _session('username_old')))
	change_password();

//
// Change username
//

if (_session('username') != _session('username_old') || (!  _session('username_old')))
	change_username(_session('id_author'), _session('username_old'), _session('username'));

//
// Insert/update author contacts
//

include_lcm('inc_contacts');
update_contacts_request('author', _session('id_author'));

if (count($_session['errors'])) {
	lcm_header("location: edit_author.php?author=" . _session('id_author'));
	exit;
}

$dest_link = new Link('author_det.php');
$dest_link->addVar('author', _session('id_author'));

// [ML] Not used at the moment, but could be useful eventually to send user
// back to where he was (but as a choice, not automatically, see author_det.php).
if (_session('ref_edit_author'))
	$dest_link->addVar('ref', _session('ref_edit_author'));

// Delete session (of form data will become ghosts)
$_SESSION['form_data'] = array();

lcm_header('Location: ' . $dest_link->getUrlForHeader());*/


if (count($_SESSION['errors'])) 
	{
	lcm_header("Location: ". $_SERVER['HTTP_REFERER']);
	exit;
	}

echo $fl;
?>
