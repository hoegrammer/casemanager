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
include_lcm('inc_obj_case');
include_lcm('inc_obj_fu');

// Clear all previous errors
$_SESSION['errors'] = array();
$_SESSION['form_data'] = array();

$zap = array();
// Get form data from POST fields
foreach($_POST as $key => $value)
    $zap[$key] = $value;

$date = $zap['start_year'].'-'.$zap['start_month'].'-'.$zap['start_day'].' '.$zap['start_hour'].':'.$zap['start_minutes'].':00';
$cripes = array();

$q= 'select c.*, cl.*
	from lcm_case as c 
	left join lcm_case_client_org as cco on c.id_case = cco.id_case
	left join lcm_client as cl on cl.id_client = cco.id_client
	where c.status = "open" and c.amount > 0
	';

$result = lcm_query($q);
while ($row=lcm_fetch_array($result))
	{
	$note=$zap['note_'.$row['id_case']];
	$check=$zap['check_'.$row['id_case']];
	$amount=$zap['amount_'.$row['id_case']];
	if ($zap['user']>0)
		$user = $zap['user'];
	else
		$user = $GLOBALS['author_session']['id_author'];
	if ($check)
		{
		$fu = new LcmFollowup();
		$fu->data['id_case']=$row['id_case'];
		$fu->data['date_start']=$date;
		$fu->data['start_year']=$zap['start_year'];
		$fu->data['start_month']=$zap['start_month'];
		$fu->data['start_day']=$zap['start_day'];
		$fu->data['start_hour']=$zap['start_hour'];
		$fu->data['start_minuite']=$zap['start_minutes'];
		$fu->data['type']='followups27';
//		$fu->data['description']='Client collects £'.$amount;
		$fu->data['outcome_amount']=$amount;
		$fu->data['bus_pass_given']=$zap['bus_pass'];
		$fu->data['user']=$user;
		$cripes[$row['id_case']]= $fu->save();
		}
	if ($note)
		{
		$fu = new LcmFollowup();
		$fu->data['id_case']=$row['id_case'];
		$fu->data['date_start']=$date;
		$fu->data['start_year']=$zap['start_year'];
		$fu->data['start_month']=$zap['start_month'];
		$fu->data['start_day']=$zap['start_day'];
		$fu->data['start_hour']=$zap['start_hour'];
		$fu->data['start_minuite']=$zap['start_minutes'];
		$fu->data['type']='followups29';
		$fu->data['description']=$note;
		$fu->data['user']=$user;
		$struth[$row['id_case']]= $fu->save();
		$q = 'INSERT INTO lcm_app SET 
				id_case = ' . $row['id_case'] . ', 
				id_author = '. $user.', 
				title = "tres", 
				description = "' . $note . '",
				date_creation = "'.$date.'"
				';
		lcm_query($q);
		$id_app = lcm_insert_id('lcm_app', 'id_app');
		$_SESSION['form_data']['id_app'] = $id_app;
		lcm_query("INSERT INTO lcm_author_app SET id_app=$id_app,id_author=" . $user);
		}
	}
if ((lcm_num_rows($cripes)>0)or(lcm_num_rows($cripes)>0))
	{
	echo "Danger: something is amiss.";
	}
foreach ($zap as $key=>$value)
	{
	if (substr($key,0,7)=='dismiss')
		{
		$id = substr($key,8,100);
		lcm_query("UPDATE lcm_app set dismissed = true where id_app =".$id);
		}
	}

lcm_header("location: tresuary2.php");


?>
