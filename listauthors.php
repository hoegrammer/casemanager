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

$find_author_string = '';
if (isset($_REQUEST['find_author_string']))
	$find_author_string = $_REQUEST['find_author_string'];

lcm_page_start(_T('title_author_list'), '', '', 'authors_intro');
matt_page_start(_T('title_author_list'));
$groups = array(
	'user' => 'Users',
	'group' => 'Groups',
	'host' => 'Hosts',
//	'befriender' => 'Befrienders',
	);
$tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'user' );
show_tabs($groups,$tab,$_SERVER['REQUEST_URI']);
//show_find_box('author', $find_author_string);
lcm_bubble($tab);


$q = "SELECT id_author,name_first,name_middle,name_last,status, right1, right2, right3, right4, right5, right6, right7, right8, right9
		FROM lcm_author ";

if ($tab == 'user')
	$q.= "WHERE (status = 'normal' or status = 'member' or status = 'special')";
elseif ($tab == 'group')
	$q.= "WHERE (status = 'group')";
elseif ($tab == 'host')
	$q.= "WHERE (status = 'host')";
elseif ($tab == 'befriender')
	$q.= "WHERE (status = 'befriender')";
	
// Add search criteria if any
//if (strlen($find_author_string)>1) {
//	$q .= " AND ((name_first LIKE '%$find_author_string%')"
//		. " OR (name_middle LIKE '%$find_author_string%')"
//		. " OR (name_last LIKE '%$find_author_string%'))";
//}
//$q .= ")";

// Sort authors by status
$order_set = false;
$order_status = '';
if (isset($_REQUEST['order_status']))
	if ($_REQUEST['order_status'] == 'ASC' || $_REQUEST['order_status'] == 'DESC') {
		$order_status = $_REQUEST['order_status'];
		$q .= " ORDER BY status " . $order_status;
		$order_set = true;
	}

// Sort authors by name_first
// [ML] I know, problably more logical by last name, but we do not split the columns
// later we can sort by any column if we need to
// [ML] 2006-03-07: Sorts using last name if siteconfig has name_order to Last, First Middle
$person_name_format = read_meta('person_name_format');
$order_name_first = 'ASC';
if (isset($_REQUEST['order_name_first']))
	if ($_REQUEST['order_name_first'] == 'ASC' || $_REQUEST['order_name_first'] == 'DESC')
		$order_name_first = $_REQUEST['order_name_first'];

$q .= ($order_set ? " , " : " ORDER BY ");

if ($person_name_format == '10')
	$q .= " name_last " . $order_name_first;
else
	$q .= " name_first " . $order_name_first;

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
$headers[0]['title'] = _Th('person_input_name');
$headers[0]['order'] = 'order_name_first';
$headers[0]['default'] = 'ASC';
if ($tab == 'user' || $tab=='group')
	{
	$headers[1]['title'] = 'Access';
	$headers[1]['order'] = 'no_order';
	$headers[2]['title'] = 'Edit';
	$headers[2]['order'] = 'no_order';
	$headers[3]['title'] = 'Admin';
	$headers[3]['order'] = 'no_order';
	$headers[4]['title'] = 'Panel';
	$headers[4]['order'] = 'no_order';
	$headers[5]['title'] = 'Accom.';
	$headers[5]['order'] = 'no_order';
	$headers[6]['title'] = 'SAL';
	$headers[6]['order'] = 'no_order';
	$headers[7]['title'] = 'Tresuary';
	$headers[7]['order'] = 'no_order';
	$headers[8]['title'] = 'NightShelter';
	$headers[8]['order'] = 'no_order';
	$headers[9]['title'] = 'Befrienders';
	$headers[9]['order'] = 'no_order';
	}

show_list_start($headers);

// Process the output of the query
for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) {
	// Author name
	echo "<tr>\n";
	echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo '<a href="author_det.php?author=' . $row['id_author'] . '" class="content_link">';
	echo highlight_matches(get_person_name($row), $find_author_string);
	echo "</a></td>\n";

	if ($tab == 'user' || $tab=='group')
		{
		// Author status
		echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
		echo ($row['right1']?'<strong>Yes</strong>':'No');
		echo "</td>\n";
		echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
		echo ($row['right2']?'<strong>Yes</strong>':'No');
		echo "</td>\n";
		echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
		echo ($row['right3']?'<strong>Yes</strong>':'No');
		echo "</td>\n";
		echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
		echo ($row['right4']?'<strong>Yes</strong>':'No');
		echo "</td>\n";
		echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
		echo ($row['right5']?'<strong>Yes</strong>':'No');
		echo "</td>\n";
		echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
		echo ($row['right6']?'<strong>Yes</strong>':'No');
		echo "</td>\n";
		echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
		echo ($row['right7']?'<strong>Yes</strong>':'No');
		echo "</td>\n";
		echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
		echo ($row['right8']?'<strong>Yes</strong>':'No');
		echo "</td>\n";
		echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
		echo ($row['right9']?'<strong>Yes</strong>':'No');
		echo "</td>\n";
		}

	echo "</tr>\n";
}

show_list_end($list_pos, $number_of_rows);

// New author button
if ($GLOBALS['author_session']['right3'])
	echo '<p><a href="edit_author.php?author=0&type='.$tab.'" class="create_new_lnk click_btn">Register new '.$tab.'</a></p>';

lcm_page_end();

?>
