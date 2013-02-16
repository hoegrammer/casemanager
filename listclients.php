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

	$Id: listclients.php,v 1.37 2006/05/26 06:54:07 mlutfy Exp $
*/

include('inc/inc.php');
include_lcm('inc_filters');
include_lcm('inc_impex');

$find_client_string = trim(_request('find_client_string'));

if (!empty($_REQUEST['export']) && ($GLOBALS['author_session']['status'] == 'admin')) {
	export('client', $_REQUEST['exp_format'], $find_client_string);
	exit;
}

lcm_page_start();
matt_page_start('Client List');
no_tabs();
show_find_box('client', $find_client_string, '', (string)($GLOBALS['author_session']['status'] == 'admin') );

// List all clients in the system + search criterion if any MATT WAS HERE: MOD SELECT TO RETRIEVE CITZ NUMBER NASS NUMBER  AND DOB
$q = "SELECT id_client,name_first,name_middle,name_last,citizen_number,nass_number,date_birth
		FROM lcm_client";

//
// Add search criteria
//
if ($find_client_string) {
	// remove useless spaces
	$find_client_string = preg_replace('/ +/', ' ', $find_client_string);

	$q .= " WHERE ((name_first LIKE '%$find_client_string%')
			OR (name_middle LIKE '%$find_client_string%')
			OR (name_last LIKE '%$find_client_string%')
			OR (CONCAT(name_first, ' ', name_middle, ' ', name_last) LIKE '%$find_client_string%')
			OR (CONCAT(name_first, ' ', name_last) LIKE '%$find_client_string%')
			OR (CONCAT(name_last, ' ', name_first) LIKE '%$find_client_string%')
			OR (citizen_number LIKE '%$find_client_string%')
			OR (date_birth LIKE '%$find_client_string%')
			OR (nass_number LIKE '%$find_client_string%')
			OR (id_client LIKE '%$find_client_string%')
		) ";//MATT WAS HERE, CLIENTS CAN BE SEARCHED BY CITZ NUM, NASS NUM, DOB, OR LASTNAME-FIRSTNAME
}

// Sort clients by ID
$order_set = false;
$order_id = '';
if (isset($_REQUEST['order_id']))
	if ($_REQUEST['order_id'] == 'ASC' || $_REQUEST['order_id'] == 'DESC') {
		$order_id = $_REQUEST['order_id'];
		$q .= " ORDER BY id_client " . $order_id;
		$order_set = true;
	}

// Sort clients by first name
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

if ($list_pos >= $number_of_rows)
	$list_pos = 0;

// Position to the page info start
if ($list_pos > 0)
	if (!lcm_data_seek($result,$list_pos))
		lcm_panic("Error seeking position $list_pos in the result");

// Output table tags
if (($number_of_rows == 0)AND(1 ==0))
	{
	echo "<p>No Clients found! Expanding Search...</p>"; 

	$q = "SELECT id_client,name_first,name_middle,name_last,citizen_number,nass_number,date_birth
			FROM lcm_client";
	
	//
	// Add search criteria
	//
	if ($find_client_string) 
		{
		// remove useless spaces
		$find_client_string = preg_replace('/ +/', ' ', $find_client_string);
		$q .= " WHERE ((name_first LIKE '%$find_client_string%')
				OR (name_middle LIKE '%$find_client_string%')
				OR (name_last LIKE '%$find_client_string%') ";

		$exploded = explode(' ',$find_client_string);
		for ($hoots = 0 ; $hoots < count($exploded) ; $hoots++)
			{
			$q .=" OR (name_first LIKE '%$exploded[$hoots]%') 
				OR (name_middle LIKE '%$exploded[$hoots]%')
				OR (name_last LIKE '%$exploded[$hoots]%')";
			}

		$q.= "

				OR (citizen_number LIKE '%$find_client_string%')
				OR (date_birth LIKE '%$find_client_string%')
				OR (nass_number LIKE '%$find_client_string%')
				OR (id_client LIKE '%$find_client_string%')
			) ";//MATT WAS HERE, CLIENTS CAN BE SEARCHED BY CITZ NUM, NASS NUM, DOB, OR LASTNAME-FIRSTNAME
		}
	
	// Sort clients by ID
	$order_set = false;
	$order_id = '';
	if (isset($_REQUEST['order_id']))
		if ($_REQUEST['order_id'] == 'ASC' || $_REQUEST['order_id'] == 'DESC') 
			{
			$order_id = $_REQUEST['order_id'];
			$q .= " ORDER BY id_client " . $order_id;
			$order_set = true;
			}
	
	// Sort clients by first name
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

	if ($list_pos >= $number_of_rows)
		$list_pos = 0;
	
	// Position to the page info start
	if ($list_pos > 0)
		if (!lcm_data_seek($result,$list_pos))
			lcm_panic("Error seeking position $list_pos in the result");
	
	}

if ($number_of_rows == 0)
	{
	echo "<p>No Clients found when searching for \"".$find_client_string."\"...</p>";
	$exploded = explode(' ',$find_client_string);
	if (count($exploded) > 1 )
		{
		for ($hoots = 0 ; $hoots < count($exploded) ; $hoots++)
			{
			$find_client_string = $exploded[$hoots];
			
			$q = "SELECT id_client,name_first,name_middle,name_last,citizen_number,nass_number,date_birth
					FROM lcm_client";

			//
			// Add search criteria
			//
			if ($find_client_string) {
				// remove useless spaces
				$find_client_string = preg_replace('/ +/', ' ', $find_client_string);
			
				$q .= " WHERE ((name_first LIKE '%$find_client_string%')
						OR (name_middle LIKE '%$find_client_string%')
						OR (name_last LIKE '%$find_client_string%')
						OR (CONCAT(name_first, ' ', name_middle, ' ', name_last) LIKE '%$find_client_string%')
						OR (CONCAT(name_first, ' ', name_last) LIKE '%$find_client_string%')
						OR (CONCAT(name_last, ' ', name_first) LIKE '%$find_client_string%')
						OR (citizen_number LIKE '%$find_client_string%')
						OR (date_birth LIKE '%$find_client_string%')
						OR (nass_number LIKE '%$find_client_string%')
						OR (id_client LIKE '%$find_client_string%')
					) ";//MATT WAS HERE, CLIENTS CAN BE SEARCHED BY CITZ NUM, NASS NUM, DOB, OR LASTNAME-FIRSTNAME
			}
			
			// Sort clients by ID
			$order_set = false;
			$order_id = '';
			if (isset($_REQUEST['order_id']))
				if ($_REQUEST['order_id'] == 'ASC' || $_REQUEST['order_id'] == 'DESC') {
					$order_id = $_REQUEST['order_id'];
					$q .= " ORDER BY id_client " . $order_id;
					$order_set = true;
				}
			
			// Sort clients by first name
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
			
			if ($list_pos >= $number_of_rows)
				$list_pos = 0;
			
			// Position to the page info start
			if ($list_pos > 0)
				if (!lcm_data_seek($result,$list_pos))
					lcm_panic("Error seeking position $list_pos in the result");
			show_page_subtitle("Showing Clients for \"".$find_client_string."\":</p>");
			if ($number_of_rows == 0)
				{
				echo "<p>No Clients</p>";
				}		
			else
				{
				show_listclient_start();
				
				for ($i = 0 ; (($i < $prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) 
					{	
					echo "<tr>\n";
					echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '">'
						. $row['id_client']
						. "</td>\n";
					echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '">';
					echo '<a href="client_det.php?client=' . $row['id_client'] . '" class="content_link">';
					$fullname = clean_output(get_person_name($row));
					echo highlight_matches($fullname, $find_client_string);
					echo "</a>\n";
					echo "</td>\n";//MATT WAS HERE ADDING CITZIEN NUMBER AND DOB TO CLIENT LISTING TABLE
					echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light"). '">';
					echo highlight_matches(clean_output($row['citizen_number']), $find_client_string);
					echo "</td>\n";
					echo "</td>\n";
					echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light"). '">';
					echo highlight_matches(clean_output($row['nass_number']), $find_client_string);
					echo "</td>\n";
					echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light"). '">';
					echo highlight_matches(clean_output(format_date($row['date_birth'],"date_short")), $find_client_string);
					echo "</td>";
					echo "</tr>\n";
					}
				show_listclient_end($list_pos, $number_of_rows);
				}
			}
		}
	}
else
	{
	show_listclient_start();
	
	for ($i = 0 ; (($i < $prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) {
		echo "<tr>\n";
		echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '">'
			. highlight_matches(clean_output($row['id_client']), $find_client_string)
			. "</td>\n";
		echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '">';
		echo '<a href="client_det.php?client=' . $row['id_client'] . '" class="content_link">';
		$fullname = clean_output(get_person_name($row));
		echo highlight_matches($fullname, $find_client_string);
		echo "</a>\n";
		echo "</td>\n";//MATT WAS HERE ADDING CITZIEN NUMBER AND DOB TO CLIENT LISTING TABLE
		echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light"). '">';
		echo highlight_matches(clean_output($row['citizen_number']), $find_client_string);
		echo "</td>\n";
		echo "</td>\n";
		echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light"). '">';
		echo highlight_matches(clean_output($row['nass_number']), $find_client_string);
		echo "</td>\n";
		echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light"). '">';
		echo highlight_matches(clean_output(format_date($row['date_birth'],"date_short")), $find_client_string);
		echo "</td>";
		echo "</tr>\n";
	}
	show_listclient_end($list_pos, $number_of_rows);
}

if ($GLOBALS['author_session']['right2'])
	{
	echo '<p><a href="edit_client.php" class="create_new_lnk click_btn">'. _T('client_button_new') .'</a></p>';
	}

if ($GLOBALS['author_session']['right3'])
	{
	echo '<p><a href="listclients2.php" class="create_new_lnk click_btn">Old Clients</a></p>';
	}

matt_page_end();
lcm_page_end();
?>
