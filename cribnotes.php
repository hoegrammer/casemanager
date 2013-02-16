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

	$Id: listauthors.php,v 1.33 2006/03/21 19:01:53 mlutfy Exp $
*/

include('inc/inc.php');
include_lcm('inc_acc');
include_lcm('inc_filters');

global $author_session;

if ($author_session['status'] != 'admin')
	{
	echo 'Not allowed';
	exit;
	}


$find_author_string = '';
if (isset($_REQUEST['find_author_string']))
	$find_author_string = $_REQUEST['find_author_string'];

lcm_page_start(_T('title_crib_list'), '', '', '');
lcm_bubble('crib_list');

$q = "SELECT *
		FROM lcm_crib";

//$order_set = false;
//$order_status = '';
//if (isset($_REQUEST['order_status']))
//	if ($_REQUEST['order_status'] == 'ASC' || $_REQUEST['order_status'] == 'DESC') {
//		$order_status = $_REQUEST['order_status'];
//		$q .= " ORDER BY status " . $order_status;
//		$order_set = true;
//	}

// Sort authors by name_first
// [ML] I know, problably more logical by last name, but we do not split the columns
// later we can sort by any column if we need to
// [ML] 2006-03-07: Sorts using last name if siteconfig has name_order to Last, First Middle
//$person_name_format = read_meta('person_name_format');
//$order_name_first = 'ASC';
//if (isset($_REQUEST['order_name_first']))
//	if ($_REQUEST['order_name_first'] == 'ASC' || $_REQUEST['order_name_first'] == 'DESC')
//		$order_name_first = $_REQUEST['order_name_first'];
//
//$q .= ($order_set ? " , " : " ORDER BY ");
//
//if ($person_name_format == '10')
//	$q .= " name_last " . $order_name_first;
//else
//	$q .= " name_first " . $order_name_first;

$result = lcm_query($q);
$number_of_rows = lcm_num_rows($result);

// Check for correct start position of the list
if (isset($_REQUEST['list_pos']))
	$list_pos = $_REQUEST['list_pos'];
else
	$list_pos = 0;

if ($list_pos>=$number_of_rows) $list_pos = 0;

// Position to the page info start
if ($list_pos>0)
	if (!lcm_data_seek($result,$list_pos))
		lcm_panic("Error seeking position $list_pos in the result");


$headers = array();
$headers[0]['title'] = 'Note';
//$headers[0]['order'] = 'order_short';
//$headers[0]['default'] = 'ASC';
$headers[1]['title'] = 'Case Type';
$headers[2]['title'] = 'Created Date';
//$headers[2]['order'] = 'order_date';
//$headers[2]['default'] = '';

show_list_start($headers);

// Process the output of the query


for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) {
	echo "<tr>\n";
	echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo "<a href='edit_crib.php?id_crib=".$row['id_crib']."'>" . $row['short'] . "</a>";
	echo "</td>\n";

	echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	$q = "SELECT title from lcm_keyword where id_keyword = ".$row['id_keyword'];
	$cresult = lcm_query($q);
	$crow = lcm_fetch_array($cresult);
	echo $crow['title'] . ($row['visible']>0?"":" <i>(hidden)</i>");
	echo "</td>\n";

	echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo $row['date_created'];
	echo "</td>\n";

	echo "</tr>\n";
}

show_list_end($list_pos, $number_of_rows);

// New author button
if ($GLOBALS['author_session']['status'] == 'admin')
	echo '<p><a href="edit_crib.php?crib=0" class="create_new_lnk">'. _T('cribedit_button_new') . "</a></p>\n";

lcm_page_end();

?>
