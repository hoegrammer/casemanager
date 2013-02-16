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

	$Id: upd_client.php,v 1.20 2006/03/17 18:03:12 mlutfy Exp $
*/


include('inc/inc.php');
include_lcm('inc_filters');
include_lcm('inc_obj_client');
include_lcm('inc_obj_case');
include_lcm('inc_obj_fu');

// Clear all previous errors
$_SESSION['errors'] = array();
$_SESSION['form_data'] = array();

// Get form data from POST fields
foreach($_POST as $key => $value)
	$_SESSION['form_data'][$key] = $value;


$ref_upd_client = 'edit_client.php?client=' . _session('id_client');

if ($_SERVER['HTTP_REFERER'])
	$ref_upd_client = $_SERVER['HTTP_REFERER'];

if (_session('id_client')==0) {$new=true;} else {$new=false;}
//
// Update data
//

if ($_SESSION['form_data']['user']==1000000)
	{
	$errs['user']="Please select a user";
	}
else
	{
	$client = new LcmClient(_session('id_client'));
	$date = 
		$_SESSION['form_data']['start_year'].'-'.
		$_SESSION['form_data']['start_month'].'-'.
		$_SESSION['form_data']['start_day'].' '.
		$_SESSION['form_data']['start_hour'].':'.
		$_SESSION['form_data']['start_minutes'].':00';
	$client->setDataString('date_update',$date);
	$errs = $client->save();
	}

if (count($errs)) {
	$_SESSION['errors'] = array_merge($_SESSION['errors'], $errs);
	lcm_header("Location: " . $ref_upd_client);
	exit;
}

//
// Add organisation
//
if (_session('new_org')) {
	$q = "REPLACE INTO lcm_client_org
		VALUES (" . _session('id_client') . ',' . _session('new_org') . ")";
	$result = lcm_query($q);
}

if ($new)
	{
	$case = new LcmCase();
	$case->data['status']='open';
	$case->data['title']='n/a';
	$case->data['stage']='na';
	$case->data['type_case']='Default';
	$cripes = $case->save();
	if (!$cripes)
		{
		$q = "INSERT INTO lcm_case_client_org SET
			id_case=".$case->getDataInt('id_case').",
			id_client=".$client->getDataInt('id_client').",
			id_org=0
			";
		lcm_query($q);
		$fu = new LcmFollowup();
		$fu->data['start_year']=$_SESSION['form_data']['start_year'];
		$fu->data['start_month']=$_SESSION['form_data']['start_month'];
		$fu->data['start_day']=$_SESSION['form_data']['start_day'];
		$fu->data['start_hour']=$_SESSION['form_data']['start_hour'];
		$fu->data['start_minute']=$_SESSION['form_data']['start_minutes'];
		$fu->data['description']=$_SESSION['form_data']['description'];
		$fu->data['id_case']=$case->getDataInt('id_case');
		$fu->data['type']='opening';
		$fu->data['user']=($_SESSION['form_data']['user']>0?$_SESSION['form_data']['user']:$GLOBALS['author_session']['id_author']);
		$cripes = $fu->save();
		}
	}
if ($_SESSION['form_data']['mode']=='scores')
	{
	$q="SELECT cco.id_case FROM lcm_case_client_org as cco LEFT JOIN lcm_case as c on cco.id_case = c.id_case WHERE c.type_case = 'default' and cco.id_client='"._session('id_client')."'";
	$result = lcm_query($q);
	$row = lcm_fetch_array($result);
	$fu = new LcmFollowup();
	$fu->data['start_year']=$_SESSION['form_data']['start_year'];
	$fu->data['start_month']=$_SESSION['form_data']['start_month'];
	$fu->data['start_day']=$_SESSION['form_data']['start_day'];
	$fu->data['start_hour']=$_SESSION['form_data']['start_hour'];
	$fu->data['start_minute']=$_SESSION['form_data']['start_minutes'];
	$fu->data['id_case']=$row['id_case'];
	$fu->data['description']=$_SESSION['form_data']['description'];
	$fu->data['type']='scores_update';
	$fu->data['user']=($_SESSION['form_data']['user']>0?$_SESSION['form_data']['user']:$GLOBALS['author_session']['id_author']);
	$cripes = $fu->save();
	}
elseif ($_SESSION['form_data']['mode']=='edit')
	{
	$q="SELECT cco.id_case FROM lcm_case_client_org as cco LEFT JOIN lcm_case as c on cco.id_case = c.id_case WHERE c.type_case = 'default' and cco.id_client='"._session('id_client')."'";
	$result = lcm_query($q);
	$row = lcm_fetch_array($result);
	$fu = new LcmFollowup();
	$fu->data['start_year']=$_SESSION['form_data']['start_year'];
	$fu->data['start_month']=$_SESSION['form_data']['start_month'];
	$fu->data['start_day']=$_SESSION['form_data']['start_day'];
	$fu->data['start_hour']=$_SESSION['form_data']['start_hour'];
	$fu->data['start_minute']=$_SESSION['form_data']['start_minutes'];
	$fu->data['id_case']=$row['id_case'];
	$fu->data['description']=$_SESSION['form_data']['description'];
	$fu->data['type']='followups43';
	$fu->data['user']=($_SESSION['form_data']['user']>0?$_SESSION['form_data']['user']:$GLOBALS['author_session']['id_author']);
	$cripes = $fu->save();
	}

//
// Go to the 'view details' page of the author
//



// small reminder, if the client was created from the "add client to case" (Case details)
//$attach = "";
//if (isset($_SESSION['form_data']['attach_case']))
//	$attach = "&attach_case=" . $_SESSION['form_data']['attach_case'];

lcm_header('Location: client_det.php?client=' . $client->getDataInt('id_client', '__ASSERT__') . $attach);
?>
