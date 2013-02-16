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

	$Id: client_det.php,v 1.55 2006/03/29 17:17:32 mlutfy Exp $
*/

include('inc/inc.php');
include_lcm('inc_obj_client');

$client = intval(_request('client'));

if (! ($client > 0))
	die("Which client?");

lcm_query('delete from lcm_client where id_client ='.$client);
lcm_query('delete from lcm_client_attachment where id_client ='.$client);
$result = lcm_query('select * from lcm_case_client_org where id_client = '.$client);
while ($row = lcm_fetch_array($result) )
	{
	lcm_query('delete from lcm_case where id_case ='.$row['id_case']);
	lcm_query('delete from lcm_case_author where id_case ='.$row['id_case']);
	lcm_query('delete from lcm_followup where id_case ='.$row['id_case']);
	lcm_query('delete from lcm_placement where id_case ='.$row['id_case']);
	}
lcm_query('delete from lcm_case_client_org where id_client ='.$client);

lcm_page_start('Deleted');
matt_page_start('Deleted');
no_tabs();
echo 'The client has been perminatly deleted.';
// Clear session info
$_SESSION['client_data'] = array(); // DEPRECATED since 0.6.4
$_SESSION['form_data'] = array();
$_SESSION['errors'] = array();
matt_page_end();
lcm_page_end();
?>
