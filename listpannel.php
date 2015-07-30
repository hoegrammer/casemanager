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
*/

include('inc/inc.php');
include_lcm('inc_obj_case');

global $author_session;
global $prefs;

lcm_page_start('Panel Managment');
matt_page_start('Panel Managment');
if ($author_session['status'] != 'admin')
	{
//	no_tabs();
//	echo "You do not have access to this area.";
//	matt_page_end();
//	lcm_page_end();
//	exit;
	}
$_SESSION['form_data']='';

$admin = false;
if ($GLOBALS['author_session']['right4']==1)
	{
	$admin = true;
	}

$groups = array 
		(
		'outstanding' => 'Post-its',
		'new' => 'Awaiting Decision',
		'rejected' => 'Rejected',
		'supported' => 'Supported',
		'reviewdue' => 'Awaiting Review',
		'termdue' => 'Upcomming Terminations',
		'terminated' => 'Terminated'
		);
$tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'outstanding' );
show_tabs($groups,$tab,$_SERVER['REQUEST_URI']);

switch ($tab)
	{
	case 'outstanding':
		include('notes.php');
		show_notes('post-panel','listpannel.php',$admin);
		break;
	case 'new':
		$type='caselist';
		$text='Newly refered, and previously defered clients.';
		$listtype=21;
		break;
	case 'old':
		$type='caselist';
		$text='List of clients who submitted an application to the Panel more than a month ago, and have not been decided.';
		$listtype=22;
		break;
	case 'rejected':
		$type='caselist';
		$text='List of clients whose application has been rejected.';
		$listtype=26;
		break;
	case 'supported':
		$type='caselist';
		$text='List of clients currently recieveing support, from the Pannel or the Accommodation Team.';
		$listtype=23;
		break;
	case 'all':
		$type='caselist';
		$text='All Panel cases, including current, old and rejected cases.';
		$listtype=24;
		break;
	case 'reviewdue':
		$type='applist';
		$text='Clients recently reviewed by Advocacy, and awaitng a Panel review.';
		$listtype='rev';
		break;
	case 'terminated':
		$type='caselist';
		$text='List of clients nolonger supported.';
		$listtype='25';
		break;
	case 'termdue':
		$type='applist';
		$text='List of clients who have a support termination date set.';
		$listtype='term';
		break;
	}

echo'<p class="normal_text">'.$text.'</p>';

if ($type=='caselist')
	{
	echo '<p class="normal_text">' . "\n";
	$case_list = new LcmCaseListUI();
	$case_list->setSearchTerm($find_case_string);
	$case_list->start($listtype,$admin);
	$case_list->printList($listtype,$admin);
	$case_list->finish();
	echo "</p>\n";
	}
if ($type=='applist')
	{
	echo "<p class=\"normal_text\">\n";

	$q = "
		SELECT fu.date_start as 'support_date', ap.*, cl.name_first, cl.name_last, c.type_case, c.id_case, cl.id_client
		FROM lcm_app as ap 
		LEFT JOIN lcm_case as c on c.id_case = ap.id_case
		LEFT JOIN lcm_case_client_org as cco on c.id_case = cco.id_case
		LEFT JOIN lcm_client as cl on cl.id_client = cco.id_client
		LEFT JOIN lcm_followup as fu on fu.id_case = c.id_case
		WHERE 1=1
		AND fu.type = 'followups20'
		AND dismissed = false
		";
	if ($listtype=='term')
		{
		$q.='AND ap.title="supterm"';
		}
	elseif($listtype=='rev')
		{
		$q.='AND ap.title="suprev"';
		}

	$sort_clauses = array();
	$sort_allow = array('ASC' => 1, 'DESC' => 1);
	if ($sort_allow[_request('d1_order')])
		$sort_clauses[] = "fu.date_start " . _request('d1_order');
	elseif ($sort_allow[_request('d2_order')])
		$sort_clauses[] = "ap.start_time " . _request('d2_order');
	if ($sort_clauses)
		$q.=" ORDER BY " . implode(', ', $sort_clauses);

	$result = lcm_query($q);
	// Get the number of rows in the result
	$number_of_rows = lcm_num_rows($result);
	if ($number_of_rows) {
		$headers = array( 
				array( 'title' => 'Client', 'order' => 'no_order'),
				array( 'title' => 'Date Supported', 'order' => 'd1_order'),
				array( 'title' => 'Date Due', 'order' => 'd2_order', 'default' => 'DESC'),
//				array( 'title' => 'Actions', 'order' => 'no_order')
//				array( 'title' => 'Status', 'order' => 'no_order'),
//				array( 'title' => _Th('app_input_type'), 'order' => 'no_order'),
//				array( 'title' => _Th('app_input_title'), 'order' => 'no_order'));
//				array( 'title' => _Th('app_input_reminder'), 'order' => 'no_order')
				);
		show_list_start($headers);
	
		// Check for correct start position of the list
		$list_pos = 0;
		
		if (isset($_REQUEST['list_pos']))
			$list_pos = $_REQUEST['list_pos'];
		
		if ($list_pos>=$number_of_rows) $list_pos = 0;
		
		// Position to the page info start
		if ($list_pos>0)
			if (!lcm_data_seek($result,$list_pos))
				lcm_panic("Error seeking position $list_pos in the result");
		
		// Show page of the list
		for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) {
			echo "\t<tr>";
			echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
				. '<a class="content_link" href="client_det.php?client='.$row['id_client'].'">'.$row['name_first'] . ' '. $row['name_last'] . '</a><br><br></td>';
			echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
				. format_date($row['support_date'], 'date_short') . '</td>';	
			echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
				. format_date($row['start_time'], 'date_short') . '</td>';
//			echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
//				. ($listtype=='rev'?	
//					'<a href="edit_fu.php?case='.$row['id_case'].'&ctype=support&type=review&ref=listpannel.php?tab=reviewdue" class="edit_lnk">Review Now</a>' :'')
//					'<a href="edit_fu.php?case='.$row['id_case'].'&ctype=support&type=stage_change&stage=terminated&ref=listpannel.php?tab=termdue" class="edit_lnk">Terminate Now</a>'
//					)
//				. '</td>';
//			echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
//				. (!$row['dismissed'] ? 'Outstanding' : 'Dismissed') . '</td>';
//						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . _Tkw('appointments', $row['type']) . '</td>';
//			echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
//				. '<a href="app_det.php?app=' . $row['id_app'] . '" class="content_link">' . $row['title'] . '</a></td>';
			// [ML] removed, not very useful.
			// echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
			//	. format_date($row['reminder'], 'short') . '</td>';
			echo "</tr>\n";
		}
	
		show_list_end($list_pos, $number_of_rows);
	}
	
	echo "</p>\n";
	}


//MATT WAS HERE. DECLUTTERING, REMOVING "NEW CLIENT/NEW CASE" BUTTONS, ADDING <BR> IN PLACE
//echo '<p><a href="edit_case.php?case=0" class="create_new_lnk">' . _T('case_button_new') . "</a></p>\n";
//echo '<p><a href="edit_client.php" class="create_new_lnk">' . _T('client_button_new') . "</a></p>\n";
//echo '<br/>';

//
// List of recent follow-ups
//


//echo '<a name="fu"></a>' . "\n";
//show_page_subtitle(_T('case_subtitle_recent_followups'));
//
//echo '<p class="normal_text">' . "\n";
//show_listfu_start('general');
//
//$q = "SELECT fu.id_case, fu.id_followup, fu.date_start, fu.date_end, fu.type, fu.description, fu.case_stage,
//			fu.hidden, a.name_first, a.name_middle, a.name_last, c.title, fu.outcome, fu.outcome_amount
//		FROM lcm_followup as fu, lcm_author as a, lcm_case as c 
//		WHERE fu.id_author = a.id_author 
//		  AND  c.id_case = fu.id_case";
//			
// Author of the follow-up
//
//	// START - Get list of cases on which author is assigned
//	$q_temp = "SELECT c.id_case
//				FROM lcm_case_author as ca, lcm_case as c
//				WHERE ca.id_case = c.id_case
//				  AND ca.id_author = " . $author_session['id_author'];
//
//	if ($prefs['case_period'] < 1900) // since X days
//		// $q_temp .= " AND TO_DAYS(NOW()) - TO_DAYS(c.date_creation) < " . $prefs['case_period'];
//		$q_temp .= " AND " . lcm_query_subst_time('c.date_creation', 'NOW()') . ' < ' . $prefs['case_period'] * 3600 * 24;
//	else // for year X
//		// $q_temp .= " AND YEAR(date_creation) = " . $prefs['case_period'];
////		$q_temp .= " AND " . lcm_query_trunc_field('c.date_creation', 'year') . ' = ' . $prefs['case_period'];
//		{
//		$q .= " AND date_start >= '".$prefs['case_period']."-04-01'";
//		$q .= " AND date_start < '".($prefs['case_period']+1)."-04-01'";
//		}
//	$r_temp = lcm_query($q_temp);
//	$list_cases = array();
//
//	while ($row = lcm_fetch_array($r_temp))
//		$list_cases[] = $row['id_case'];
//	// END - Get list of cases on which author is assigned
//
//if (! ($prefs['case_owner'] == 'all' && $author_session['status'] == 'admin')) {
//	$q .= " AND ( ";
//
//	if ($prefs['case_owner'] == 'public')
//		$q .= " c.public = 1 OR ";
//
//	// [ML] XXX FIXME TEMPORARY PATCH
//	// if user and no cases + no follow-ups...
//	if (count($list_cases))
//		$q .= " fu.id_case IN (" . implode(",", $list_cases) . "))";
//	else
//		$q .= " fu.id_case IN ( 0 ))";
//	
//}
//
// Period (date_creation) to show
//if ($prefs['case_period'] < 1900) // since X days
//	// $q .= " AND TO_DAYS(NOW()) - TO_DAYS(date_start) < " . $prefs['case_period'];
//	{
//	$q .= " AND " . lcm_query_subst_time('date_start', 'NOW()') . ' < ' . $prefs['case_period'] * 3600 * 24;
//	}
//else // for year X
////	 $q .= " AND YEAR(date_start) = " . $prefs['case_period'];
////	$q .= " AND " . lcm_query_trunc_field('date_start', 'year') . ' = ' . $prefs['case_period'];
//	{
//	$q .= " AND date_start >= '".$prefs['case_period']."-04-01'";
//	$q .= " AND date_start < '".($prefs['case_period']+1)."-04-01'";
//	}
// MATT WAS HERE, ADDING "OPEN/CLOSED" STATUS FILTER TO FOLLOWUPS SECTION OF LIST CASES PAGE
//if (!($prefs['case_status']=='all'))
//	{
//	$q .= " AND (c.status ='".$prefs['case_status']."') ";
//	}
//
// Add ordering
//$fu_order = "DESC";
//if (isset($_REQUEST['fu_order']))
//	if ($_REQUEST['fu_order'] == 'ASC' || $_REQUEST['fu_order'] == 'DESC')
//		$fu_order = $_REQUEST['fu_order'];
//
//$q .= " ORDER BY date_start $fu_order, id_followup $fu_order";
//$result = lcm_query($q);
//
// Check for correct start position of the list
//$number_of_rows = lcm_num_rows($result);
//$fu_list_pos = 0;
//		
//if (isset($_REQUEST['fu_list_pos']))
//	$fu_list_pos = $_REQUEST['fu_list_pos'];
//				
//if ($fu_list_pos >= $number_of_rows)
//	$fu_list_pos = 0;
//				
// Position to the page info start
//if ($fu_list_pos > 0)
//	if (!lcm_data_seek($result,$fu_list_pos))
//		lcm_panic("Error seeking position $fu_list_pos in the result");
//			
// Process the output of the query
//for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))); $i++)
//	show_listfu_item($row, $i, 'general');
//
//show_list_end($fu_list_pos, $number_of_rows, false, 'fu');
//echo "</p>\n";
matt_page_end();
lcm_page_end();

?>
