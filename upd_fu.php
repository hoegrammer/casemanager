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

$id_followup = intval(_request('id_followup', 0));

// Get form data from POST fields
foreach($_POST as $key => $value)
    $_SESSION['form_data'][$key]=$value;

if ($_SESSION['form_data']['eyedee_case'])
	{
	$_SESSION['form_data']['id_case']=$_SESSION['form_data']['eyedee_case'];
	}

// Get old FU data, if updating
$old_fu_data = array();

if ($id_followup) {
	$q = "SELECT *
			FROM lcm_followup
			WHERE id_followup = $id_followup";

	$result = lcm_query($q);

	if (! ($old_fu_data = lcm_fetch_array($result)))
		lcm_panic("Could not find requested follow-up");
}

///////////////////////////////////////////////////////////////////////
//	Consequent appointment information error checking
///////////////////////////////////////////////////////////////////////
if (isset($_SESSION['form_data']['add_appointment'])) 
	{
	// Convert day, month, year, hour, minute to date/time
	// Check submitted information
	if ($_SESSION['form_data']['add_appointment']=='suprev')
		{
		$_SESSION['form_data']['app_title']='suprev';
		}
	elseif ($_SESSION['form_data']['add_appointment']=='supterm')
		{
		$_SESSION['form_data']['app_title']='supterm';
		}
	elseif ($_SESSION['form_data']['add_appointment']=='accrev')
		{
		$_SESSION['form_data']['app_title']='accrev';
		}
	elseif ($_SESSION['form_data']['add_appointment']=='accterm')
		{
		$_SESSION['form_data']['app_title']='accterm';
		}
	elseif ($_SESSION['form_data']['add_appointment']=='salrev')
		{
		$_SESSION['form_data']['app_title']='salrev';
		}
	else
		{
		echo "STOP!";
		$_SESSION['form_date']['add_appointment']='error is here!';
		}
	//
	// Start time
	//
	if ($_SESSION['form_data']['type']=='salreview'||$_SESSION['form_data']['bugfix']=='salreview')
		{
		$_SESSION['form_data']['app_start_time'] = get_datetime_from_array($_SESSION['form_data'], 'start', 'start', '', false);
		}
	else
		{
		$_SESSION['form_data']['app_start_time'] = get_datetime_from_array($_SESSION['form_data'], 'app_start', 'start', '', false);
		}

	$unix_app_start_time = strtotime($_SESSION['form_data']['app_start_time']);
	
	if (($unix_app_start_time<0) || ! checkdate_sql($_SESSION['form_data']['app_start_time']))
		$_SESSION['errors']['app_start_time'] = 'Invalid appointment start time!'; // TRAD
	
//	//
//	// End time
//	//
//	if ($prefs['time_intervals'] == 'absolute') {
//		// Set to default empty date if all fields empty
//		if (!($_SESSION['form_data']['app_end_year'] || $_SESSION['form_data']['app_end_month'] || $_SESSION['form_data']['app_end_day']))
//			$_SESSION['form_data']['app_end_time'] = '0000-00-00 00:00:00';
//			// Report error if some of the fields empty TODO
//		elseif (!$_SESSION['form_data']['app_end_year'] || !$_SESSION['form_data']['app_end_month'] || !$_SESSION['form_data']['app_end_day']) {
//			$_SESSION['errors']['app_end_time'] = 'Partial appointment end time!';
//			$_SESSION['form_data']['app_end_time'] = get_datetime_from_array($_SESSION['form_data'], 'app_end', 'start', '', false);
//		} else {
//			// Join fields and check resulting date
//			$_SESSION['form_data']['app_end_time'] = get_datetime_from_array($_SESSION['form_data'], 'app_end', 'start', '', false);
//			$unix_app_end_time = strtotime($_SESSION['form_data']['app_end_time']);
//	
//			if ( ($unix_app_end_time<0) || !checkdate($_SESSION['form_data']['app_end_month'],$_SESSION['form_data']['app_end_day'],$_SESSION['form_data']['app_end_year']) )
//				$_SESSION['errors']['app_end_time'] = 'Invalid appointment end time!';
//		}
//	} else {
//		if ( ! (isset($_SESSION['form_data']['app_delta_days']) && (!is_numeric($_SESSION['form_data']['app_delta_days']) || $_SESSION['form_data']['app_delta_days'] < 0) ||
//			isset($_SESSION['form_data']['app_delta_hours']) && (!is_numeric($_SESSION['form_data']['app_delta_hours']) || $_SESSION['form_data']['app_delta_hours'] < 0) ||
//			isset($_SESSION['form_data']['app_delta_minutes']) && (!is_numeric($_SESSION['form_data']['app_delta_minutes']) || $_SESSION['form_data']['app_delta_minutes'] < 0) ) ) {
//			$unix_app_end_time = $unix_app_start_time
//					+ $_SESSION['form_data']['app_delta_days'] * 86400
//					+ $_SESSION['form_data']['app_delta_hours'] * 3600
//					+ $_SESSION['form_data']['app_delta_minutes'] * 60;
//			$_SESSION['form_data']['app_end_time'] = date('Y-m-d H:i:s', $unix_app_end_time);
//		} else {
//			$_SESSION['errors']['app_end_time'] = _Ti('app_input_time_length') . _T('time_warning_invalid_format') . ' (' . $_SESSION['form_data']['app_delta_hours'] . ')'; // XXX
//			$_SESSION['form_data']['app_end_time'] = $_SESSION['form_data']['app_start_time'];
//		}
//	}
//	
//	// reminder
//	if ($prefs['time_intervals']=='absolute') {
//		// Set to default empty date if all fields empty
//		if (!($_SESSION['form_data']['app_reminder_year'] || $_SESSION['form_data']['app_reminder_month'] || $_SESSION['form_data']['app_reminder_day']))
//			$_SESSION['form_data']['app_reminder'] = '0000-00-00 00:00:00';
//			// Report error if some of the fields empty
//		elseif (!$_SESSION['form_data']['app_reminder_year'] || !$_SESSION['form_data']['app_reminder_month'] || !$_SESSION['form_data']['app_reminder_day']) {
//			$_SESSION['errors']['app_reminder'] = 'Partial appointment reminder time!'; // TRAD
//			$_SESSION['form_data']['app_reminder'] = get_datetime_from_array($_SESSION['form_data'], 'app_reminder', 'start', '', false);
//		} else {
//			// Join fields and check resulting time
//			$_SESSION['form_data']['app_reminder'] = get_datetime_from_array($_SESSION['form_data'], 'app_reminder', 'start', '', false);
//			$unix_app_reminder_time = strtotime($_SESSION['form_data']['app_reminder']);
//	
//			if ( ($unix_app_reminder_time<0) || !checkdate($_SESSION['form_data']['app_reminder_month'],$_SESSION['form_data']['app_reminder_day'],$_SESSION['form_data']['app_reminder_year']) )
//				$_SESSION['errors']['app_reminder'] = 'Invalid appointment reminder time!'; // TRAD
//		}
//	} else {
//		if ( ! (isset($_SESSION['form_data']['app_rem_offset_days']) && (!is_numeric($_SESSION['form_data']['app_rem_offset_days']) || $_SESSION['form_data']['app_rem_offset_days'] < 0) ||
//			isset($_SESSION['form_data']['app_rem_offset_hours']) && (!is_numeric($_SESSION['form_data']['app_rem_offset_hours']) || $_SESSION['form_data']['app_rem_offset_hours'] < 0) ||
//			isset($_SESSION['form_data']['app_rem_offset_minutes']) && (!is_numeric($_SESSION['form_data']['app_rem_offset_minutes']) || $_SESSION['form_data']['app_rem_offset_minutes'] < 0) ) ) {
//			$unix_app_reminder_time = $unix_app_start_time
//					- $_SESSION['form_data']['app_rem_offset_days'] * 86400
//					- $_SESSION['form_data']['app_rem_offset_hours'] * 3600
//					- $_SESSION['form_data']['app_rem_offset_minutes'] * 60;
//			$_SESSION['form_data']['app_reminder'] = date('Y-m-d H:i:s', $unix_app_reminder_time);
//		} else {
//			$_SESSION['errors']['app_reminder'] = _Ti('app_input_reminder') . _T('time_warning_invalid_format') . ' (' . $_SESSION['form_data']['app_rem_offset_hours'] . ')'; // XXX
//			$_SESSION['form_data']['app_reminder'] = $_SESSION['form_data']['app_start_time'];
//		}
//	}
//	
	// title
	if (! $_SESSION['form_data']['app_title'])
		$_SESSION['errors']['app_title'] = _Ti('app_input_title') . _T('warning_field_mandatory');
}


//
// Check if any errors found
//
if (count($_SESSION['errors'])) {
    lcm_header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

///////////////////////////////////////////////////////////////////////
// UPDATEING CLIENT DETAILS AFTER A SAL REVIEW
///////////////////////////////////////////////////////////////////////
if ($_SESSION['form_data']['bugfix']=='salreview'||$_SESSION['form_data']['type']=='followups39')
	{	
	$client = new LcmClient($_SESSION['form_data']['id_client']);
	$date = 
		$_SESSION['form_data']['start_year'].'-'.
		$_SESSION['form_data']['start_month'].'-'.
		$_SESSION['form_data']['start_day'].' '.
		$_SESSION['form_data']['start_hour'].':'.
		$_SESSION['form_data']['start_minutes'].':00';
	$client->setDataString('date_update',$date);
	$errs = $client->save();
	}



///////////////////////////////////////////////////////////////////////
//	Followup information update
///////////////////////////////////////////////////////////////////////

//if ($_SESSION['form_data']['add_appointment']=='supterm')
//	{
//	$_SESSION['form_data']['stage']='supported_notice';
//	}
//
//if ($_SESSION['form_data']['add_appointment']=='accterm')
//	{
//	$_SESSION['form_data']['stage']='accom_notice';
//	}

//echo "<hr>";

$fu = new LcmFollowup($id_followup);

		
if ($_SESSION['form_data']['eyedee_case'])
	{
	$fu->data['id_case']=$_SESSION['form_data']['eyedee_case'];
	}
$errs = $fu->save();

//exit;
if (count ($errs))
	$_SESSION['errors'] = array_merge($_SESSION['errors'], $errs);

if (count($_SESSION['errors'])) {
    lcm_header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

if (! $id_followup)
	$id_followup = $fu->getDataInt('id_followup', '__ASSERT__');
//
// Update stage keywords
//
if (isset($_REQUEST['new_stage']) && $_REQUEST['new_stage']) {
	$stage_info = get_kw_from_name('stage', $_REQUEST['new_stage']);
	$id_stage = $stage_info['id_keyword'];
	update_keywords_request('stage', $_SESSION['form_data']['id_case'], $id_stage);
}

//
// Update lcm_case.date_update (if fu.date_start > c.date_update)
//
// MATT WAS HERE. UPDATE CLIENT LAST WORK WHEN FOLLOWUP IS CREATED/EDITED AND SAVED.
$matt = "UPDATE lcm_client as cl LEFT JOIN lcm_case_client_org as cco ON cl.id_client = cco.id_client SET cl.last_work = NOW() WHERE cco.id_case = "
	.$fu->getDataInt('id_case'); 
lcm_query($matt);
$matt = "SELECT cl.id_client FROM lcm_client as cl NATURAL JOIN lcm_case_client_org as cco WHERE cco.id_case=".$fu->getDataInt('id_case');
$result=lcm_query($matt);
$row= lcm_fetch_array($result);
$client = $row['id_client'];

$q = "SELECT date_update FROM lcm_case WHERE id_case = " . $fu->getDataInt('id_case', '__ASSERT__');
$result = lcm_query($q);

if ($_SESSION['form_data']['setamount']=='yes')
	{
	lcm_query('update lcm_case set amount='.$_SESSION['form_data']['amount'].' where id_case ='.$fu->getDataInt('id_case'));
	$buspass=($_SESSION['form_data']['buspass']?'yes':'no');
	lcm_query('update lcm_case set legal_reason="'.$buspass.'" where id_case ='.$fu->getDataInt('id_case'));
//	lcm_query('update lcm_followup set description=concat("£'.$_SESSION['form_data']['amount'].' ",,description ) where id_followup ='.$fu->getDataInt('id_followup'));
	$_SESSION['form_data']['details'] = "£".$_SESSION['form_data']['amount'] . "\n" . $_SESSION['form_data']['details'];
	}

if (($row = lcm_fetch_array($result))) {
	if ($fu->getDataString('date_start', '__ASSERT__') > $row['date_update']) {
		$q = "UPDATE lcm_case
				SET date_update = '" . $fu->getDatastring('date_start') . "'
				WHERE id_case = " . $fu->getDataInt('id_case', '__ASSERT__');

		lcm_query($q);
	}
} else {
	lcm_panic("Query returned no results.");
}


$zomg  = $fu->getDataString('type');
if ($zomg == 'followups21')//normal review
	{
	$q= "UPDATE lcm_app SET dismissed = true where id_case='".$fu->getDataString('id_case')."' AND (
		(title='suprev')OR
		(title='accrev')OR
		(title='salrev')OR
		(title='accterm')OR
		(title='supterm'))";
	lcm_query($q);
	}
elseif ($zomg == 'followups39')//sal review
	{
	$q= "UPDATE lcm_app SET dismissed = true where id_case='".$fu->getDataString('id_case')."' AND (
		(title='suprev')OR
		(title='accrev')OR
		(title='salrev'))";
	lcm_query($q);
	}
elseif ($zomg == 'followups22'||$zomg == 'followups19'||$zomg == 'followups24')//termination||rejection||moved-in
	{
	$q= "UPDATE lcm_app SET dismissed = true where id_case='".$fu->getDataString('id_case')."' AND (
		(title='suprev')OR
		(title='accrev')OR
		(title='salrev')OR
		(title='accterm')OR
		(title='supterm'))";
	lcm_query($q);
	}
///////////////////////////////////////////////////////////////////////
//	Consequent appointment information update                        //
///////////////////////////////////////////////////////////////////////
if (isset($_SESSION['form_data']['add_appointment'])) 
	{
	// No errors, proceed with database update
	$fl="type		= '" . clean_input($_SESSION['form_data']['app_type']) . "',
		title		= '" . clean_input($_SESSION['form_data']['app_title']) . "',
		description	= '" . clean_input($_SESSION['form_data']['app_description']) . "',
		start_time	= '" . $_SESSION['form_data']['app_start_time'] . "',
		end_time	= '" . $_SESSION['form_data']['app_end_time'] . "',
		reminder	= '" . $_SESSION['form_data']['app_reminder'] . "'
		";
	
	// Add the new appointment
	$q = "INSERT INTO lcm_app SET ";
	// Add case ID
	$q .= 'id_case = ' . $_SESSION['form_data']['id_case'] . ',';
	// Add ID of the creator
	$q .= 'id_author = ' . $GLOBALS['author_session']['id_author'] . ',';
	// Add the rest of the fields
	$q .= "$fl, date_creation = NOW()";

	$result = lcm_query($q);


	// Get new appointment's ID
	$id_app = lcm_insert_id('lcm_app', 'id_app');
	$_SESSION['form_data']['id_app'] = $id_app;

	// Add relationship with the creator
	lcm_query("INSERT INTO lcm_author_app SET id_app=$id_app,id_author=" . $GLOBALS['author_session']['id_author']);

	// Add followup relation
	lcm_query("INSERT INTO lcm_app_fu SET id_app=$id_app,id_followup=$id_followup,relation='parent'");
	}	



///////////////////////////////////////////////////////////////////////
//	Set up Post-it note, if neccessary
///////////////////////////////////////////////////////////////////////
$postit = false;
if ($_SESSION['form_data']['type']=='followups34'||$_SESSION['form_data']['cc'])
	{
	$_SESSION['form_data']['app_start_time'] = get_datetime_from_array($_SESSION['form_data'], 'start', 'start', '', false);
	$q = 'INSERT INTO lcm_app SET 
			id_case = ' . $_SESSION['form_data']['id_case'] . ', 
			id_author = ' .	($_SESSION['form_data']['user']>0?$_SESSION['form_data']['user']:$GLOBALS['author_session']['id_author']) . ',
			title = "' . $_SESSION['form_data']['cc']  . '", 
			colour = "' . $_SESSION['form_data']['colour']  . '", 
			description = "' . $_SESSION['form_data']['description'] . '",
			date_creation = "'.$_SESSION['form_data']['app_start_time'] . '"
			';
	$postit=true;
	lcm_query($q);
	$id_app = lcm_insert_id('lcm_app', 'id_app');
	$_SESSION['form_data']['id_app'] = $id_app;
	lcm_query("INSERT INTO lcm_author_app SET id_app=$id_app,id_author=" . $GLOBALS['author_session']['id_author']);
	}


if (($_SESSION['form_data']['app_title']=='accterm'))
	{
	$_SESSION['form_data']['app_start_time'] = get_datetime_from_array($_SESSION['form_data'], 'start', 'start', '', false);
	$q = 'INSERT INTO lcm_app SET 
			id_case = ' . $_SESSION['form_data']['id_case'] . ', 
			id_author = ' .	($_SESSION['form_data']['user']>0?$_SESSION['form_data']['user']:$GLOBALS['author_session']['id_author']) . ',
			title = "post-admin", 
			colour = "yellow", 
			description = "This client\'s accomidation has been marked for termination. Please notify the client.",
			date_creation = "'.$_SESSION['form_data']['app_start_time'] . '"
			';
	$postit=true;
	lcm_query($q);
	$id_app = lcm_insert_id('lcm_app', 'id_app');
	$_SESSION['form_data']['id_app'] = $id_app;
	lcm_query("INSERT INTO lcm_author_app SET id_app=$id_app,id_author=" . $GLOBALS['author_session']['id_author']);
	}

if ($postit==true && 1==0);
	{
	}

//$_SESSION['form_data']['app_start_time'] = get_datetime_from_array($_SESSION['form_data'], 'app_start', 'start', '', false);

///////////////////////////////////////////////////////////////////////
//	Set up Befriender Details, if neccessary
//////////////////////////////////////////////////////////////////////
if ($_SESSION['form_data']['bef'])
	{
	if ($_SESSION['form_data']['id_case'])
		{
		lcm_query('UPDATE lcm_case set notes = '.$_SESSION['form_data']['bef'].' where id_case = '.$_SESSION['form_data']['id_case']);	
		}
	else
		{
		print "Error occoured, doom";
		}
	}

///////////////////////////////////////////////////////////////////////
//	Set up Placement Details, if neccessary
//////////////////////////////////////////////////////////////////////
$cancelpannel = false;
if ($_SESSION['form_data']['id_room'])
	{
	if ($_SESSION['form_data']['stage']=='accomreserved')
		{
		$q = "INSERT into lcm_placement set 
			id_room=".$_SESSION['form_data']['id_room'].", 
			id_case=".$_SESSION['form_data']['id_case'].","; 
		$q.="date_start='".
				$_SESSION['form_data']['start_year']."-".
				$_SESSION['form_data']['start_month']."-".
				$_SESSION['form_data']['start_day']."', ";
		$q.="status='provisional'";
		}
	elseif ($_SESSION['form_data']['stage']=='reserved')
		{
		$q = "INSERT into lcm_placement set 
			id_room=".$_SESSION['form_data']['id_room'].", 
			id_case=".$_SESSION['form_data']['id_case'].","; 
		$q.="date_start='".
				$_SESSION['form_data']['start_year']."-".
				$_SESSION['form_data']['start_month']."-".
				$_SESSION['form_data']['start_day']."', ";
		$q.="status='provisional'";
		}
	elseif ($_SESSION['form_data']['type']=='followups42')//stage also == accom, but catch first with followups check for when 
		{												//  we are canceling a move between houses reservation
		$q = "UPDATE lcm_placement set 
			id_room=".$_SESSION['form_data']['id_room'].", 
			id_case=".$_SESSION['form_data']['id_case'].","; 
		$q.="date_end='".
				$_SESSION['form_data']['start_year']."-".
				$_SESSION['form_data']['start_month']."-".
				$_SESSION['form_data']['start_day']."', 
			status='declined' ";
		$q.="where id_room=".$_SESSION['form_data']['id_room']." ";
		$q.="and status='provisional'";
		}
	elseif ($_SESSION['form_data']['stage']=='accom' || $_SESSION['form_data']['stage']=='accom_notice')
		{
		lcm_query("DELETE from lcm_placement where id_room=".$_SESSION['form_data']['id_room']." and status='provisional'");
		lcm_query("UPDATE lcm_placement set status='terminated' where id_case=".$_SESSION['form_data']['id_case']." and status='active'");
		$q = "INSERT into lcm_placement set 
			id_room=".$_SESSION['form_data']['id_room'].", 
			id_case=".$_SESSION['form_data']['id_case'].","; 
		$q.="date_start='".
				$_SESSION['form_data']['start_year']."-".
				$_SESSION['form_data']['start_month']."-".
				$_SESSION['form_data']['start_day']."', 
			status="."'active'";
		$cancelpannel = true;
		}
	elseif ($_SESSION['form_data']['stage']=='accom')
		{
		lcm_query("DELETE from lcm_placement where id_room=".$_SESSION['form_data']['id_room']." and status='provisional'");
		lcm_query("UPDATE lcm_placement set status='terminated' where id_case=".$_SESSION['form_data']['id_case']." and status='active'");
		$q = "INSERT into lcm_placement set 
			id_room=".$_SESSION['form_data']['id_room'].", 
			id_case=".$_SESSION['form_data']['id_case'].","; 
		$q.="date_start='".
				$_SESSION['form_data']['start_year']."-".
				$_SESSION['form_data']['start_month']."-".
				$_SESSION['form_data']['start_day']."', 
			status="."'active'";
		$cancelpannel = true;
		}
	elseif ($_SESSION['form_data']['type']=='followups26')
		{
		$q = "UPDATE lcm_placement set 
			id_room=".$_SESSION['form_data']['id_room'].", 
			id_case=".$_SESSION['form_data']['id_case'].","; 
		$q.="date_end='".
				$_SESSION['form_data']['start_year']."-".
				$_SESSION['form_data']['start_month']."-".
				$_SESSION['form_data']['start_day']."', 
			status='declined' ";
		$q.="where id_room=".$_SESSION['form_data']['id_room']." ";
		$q.="and status='provisional'";
		}
	else
		{
		print "OI NOI SAVELOY!";
		exit;
		}
	$result=lcm_query($q);
	}
if (($_SESSION['form_data']['stage']=='terminated')&&($_SESSION['form_data']['ctype']=='accomidation'))
	{
	$q = "UPDATE lcm_placement SET
		date_end='".
			$_SESSION['form_data']['start_year']."-".
			$_SESSION['form_data']['start_month']."-".
			$_SESSION['form_data']['start_day']."', 
		status= 'terminated'
		WHERE id_case=".$_SESSION['form_data']['id_case']." 
		AND status= 'active'";
	$result=lcm_query($q);
	$q = "UPDATE lcm_placement SET
		date_end='".
			$_SESSION['form_data']['start_year']."-".
			$_SESSION['form_data']['start_month']."-".
			$_SESSION['form_data']['start_day']."', 
		status="."'declined'
		WHERE id_case=".$_SESSION['form_data']['id_case']."
		AND status='provisional'";
	$result=lcm_query($q);// don't forget to get rid of between-house movement reservations when terminating a client
	}

if ($cancelpannel)
	{
	$q = "
		select cco.id_case, foo.id_client, c.type_case, c.status, c.stage
		from (select id_client from lcm_case_client_org where id_case = ".$_SESSION['form_data']['id_case'].") as foo
		left join lcm_case_client_org as cco on cco.id_client = foo.id_client
		left join lcm_case as c on c.id_case = cco.id_case
		where c.type_case='Support' and c.status='open'
		";
	$result = lcm_query($q);
	if ($_SESSION['form_data']['user']>0)
		$user = $_SESSION['form_data']['user'];
	else
		$user = $GLOBALS['author_session']['id_author'];
	while ($row = lcm_fetch_array($result))
		{
		if ($row['stage']=='supported')
			{
			lcm_query('update lcm_case set stage="terminated" and status="closed" where id_case = ' . $row['id_case']);
			lcm_query('update lcm_app set dismissed = true where id_case = '.$row['id_case']);
			$fu = new LcmFollowup();
			$fu->data['id_case']=$row['id_case'];
			$fu->data['date_start']= 
				$_SESSION['form_data']['start_year'].'-'.
				$_SESSION['form_data']['start_month'].'-'.
				$_SESSION['form_data']['start_day'].' '.
				$_SESSION['form_data']['start_hour'].':'.
				$_SESSION['form_data']['start_minutes'].':00';
			$fu->data['type']='followups22';
			$fu->data['description']='Moving into ASSIST housing automatically terminates Pannel support.';
			$fu->data['user']=$user;
			$cripes[$row['id_case']]= $fu->save();
			}
		if (($row['stage']=='submitted')||($row['stage']=='submitted2')||($row['stage']=='submitted3')||($row['stage']=='submitted4'))
			{
			lcm_query('update lcm_case as c set c.stage=\'rejected\' and c.status=\'closed\' where c.id_case = '.$row['id_case']);
			lcm_query('update lcm_app set dismissed = true where id_case = '.$row['id_case']);
			$fu = new LcmFollowup();
			$fu->data['id_case']=$row['id_case'];
			$fu->data['date_start']= 
				$_SESSION['form_data']['start_year'].'-'.
				$_SESSION['form_data']['start_month'].'-'.
				$_SESSION['form_data']['start_day'].' '.
				$_SESSION['form_data']['start_hour'].':'.
				$_SESSION['form_data']['start_minutes'].':00';
			$fu->data['type']='followups22';
			$fu->data['description']='Moving into ASSIST housing automatically rejects application for Pannel support.';
			$fu->data['user']=$user;
			$cripes[$row['id_case']]= $fu->save();
			}
		}
	}

// TO DO HERE. TERMINATE SUPPORT CASES, CANCEL APPLICATIONS FOR SUPPORT, KILL ANY OUTSTANDING APPOINTMENTS FOR THOSE CASES, ADD WORK.




// Send user back to add/edit page's referer or (default) to followup detail page
//lcm_header('Location: fu_det.php?followup=' . $id_followup);
if ($_SESSION['form_data']['ref_edit_fu'])
	{
	lcm_header("Location: ".$_SESSION['form_data']['ref_edit_fu']);
	}
else
	{
	lcm_header('Location: client_det.php?client=' . $client);
	}
exit;

?>
