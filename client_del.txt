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
include_lcm('inc_contacts');
include_lcm('inc_obj_client');
include_lcm('inc_obj_case');
include_lcm('inc_obj_fu');

$client = intval(_request('client'));

if (! ($client > 0))
	die("Which client?");

$q="SELECT *
	FROM lcm_client as c
	WHERE c.id_client = $client";

$result = lcm_query($q);

if (! ($row = lcm_fetch_array($result)))
	die("There's no such client.");

$first=$row['name_first'];
lcm_page_start('Delete Client: '. get_person_name($row), '', '', 'clients_intro');
matt_page_start('Delete Client: '.get_person_name($row));
no_tabs();
echo '<p>';
echo "<b>Are you sure you want to delete ".get_person_name($row)."?</b>";
echo '</p>';

echo '<p>';
echo "Deleteing a client will <b>completely remove any trace that they had ever existed</b> on the system. It will delete their personal details, their file and all work contained within it. It will also delete any uploaded files, accommodation placements, befriender matches, post-it notes, and welfare payments related to the client.";
echo '</p>';

echo '<p>';
echo "This action is <b>not reversable</b>. If you are absolutly sure you want to delete the client, press the button below.";
echo '</p>';

echo '<p><a href="client_del2.php?client='.$client.'" class="edit_lnk">Delete '.get_person_name($row).'</a></p>';
				
// Clear session info
$_SESSION['client_data'] = array(); // DEPRECATED since 0.6.4
$_SESSION['form_data'] = array();
$_SESSION['errors'] = array();
matt_page_end();
lcm_page_end();
?>
