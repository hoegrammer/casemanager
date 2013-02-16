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
foreach($_POST as $key => $value)
    $_SESSION['form_data'][$key] = $value;


$fl='';

if ($_SESSION['form_data']['name']=='')
	{
	$_SESSION['errors']['name']='Room Needs a Name';
	}
else
	{
	$fl .= 'name="'.$_SESSION['form_data']['name'].'", ';
	}

$fl .= 'type="'.$_SESSION['form_data']['type'].'", ';
$fl .= 'host="'.$_SESSION['form_data']['host'].'", ';
$fl .= 'sex="'.$_SESSION['form_data']['sex'].'", ';
$fl .= 'note="'.$_SESSION['form_data']['note'].'", ';
if ($_SESSION['form_data']['available']=='yes')
	{
	$fl .= 'status="normal" ';
	}
else
	{
	$fl .= 'status="unavailable" ';
	}


if (count($_SESSION['errors'])) 
	{
	lcm_header("Location: ". $_SERVER['HTTP_REFERER']);
	exit;
	}

if ($_SESSION['form_data']['id_room'] == 0)
	{
	$q= "INSERT INTO lcm_room SET ".$fl;
	}
else
	{
	$q= "UPDATE lcm_room SET ".$fl." WHERE id_room = ".$_SESSION['form_data']['id_room'];
	}
$result = lcm_query($q);

$_SESSION['form_data']=array();
lcm_header('Location: listrooms.php');
