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
include_lcm('inc_obj_fu');
include('notes.php');

global $author_session;


$_SESSION['form_data']=array();
$_SESSION['errors']=array();

lcm_page_start('ADVOCACY');
matt_page_start('ADVOCACY');
$groups = array(
	'outstanding' => 'Post-its',
	'reviewdue'=> 'Awaiting review'
//	'dismissed' => 'Dismissed',
	);
$tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'outstanding' );
show_tabs($groups,$tab,$_SERVER['REQUEST_URI']);

$admin = false;
if ($GLOBALS['author_session']['right6']==1)
	{
	$admin = true;
	}

switch ($tab)
	{
	case 'outstanding':
		show_notes('post-sal','sal.php',$admin);
		break;
	case 'reviewdue':
		echo "<p class=\"normal_text\">\n";
		$q = "
			SELECT fu.date_start as 'support_date', ap.*, cl.name_first, cl.name_last, c.type_case, c.id_case, cl.id_client
			FROM lcm_app as ap 
			LEFT JOIN lcm_case as c on c.id_case = ap.id_case
			LEFT JOIN lcm_case_client_org as cco on c.id_case = cco.id_case
			LEFT JOIN lcm_client as cl on cl.id_client = cco.id_client
			LEFT JOIN lcm_followup as fu on fu.id_case = c.id_case
			WHERE 1=1
			AND ((fu.type = 'followups20')OR(fu.type='followups24'))
			AND dismissed = false
			AND ap.title='salrev'";

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
		if ($number_of_rows) 
			{			
			$headers = array( 
					array( 'title' => 'Client', 'order' => 'no_order'),
					array( 'title' => 'Date Supported', 'order' => 'd1_order'),
					array( 'title' => 'Date Review Due', 'order' => 'd2_order', 'default' => 'DESC'),
//					array( 'title' => 'Actions', 'order' => 'no_order')
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
			for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) 
				{
				echo "\t<tr>";
				echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
					. '<a class="content_link" href="client_det.php?client='.$row['id_client'].'">'.$row['name_first'] . ' '. $row['name_last'] . '</a><br><br></td>';
				echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
					. format_date($row['support_date'], 'date_short') . '</td>';	
				echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
					. format_date($row['start_time'], 'date_short') . '</td>';
//				echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
//					. ($listtype=='rev'?	
//						'<a href="edit_fu.php?case='.$row['id_case'].'&ctype=support&type=review&ref=listpannel.php?tab=reviewdue" class="edit_lnk">Review Now</a>' :'')
//					. '</td>';
				echo "</tr>\n";
				}
			show_list_end($list_pos, $number_of_rows);
			}
		break;
	case 'dismissed':
		$order='ap.date_creation';
		$dir='DESC';
		if (isset($_REQUEST['date_order']))
			{$order = 'ap.date_creation';$dir=$_REQUEST['date_order'];}
		elseif (isset($_REQUEST['client_order']))
			{$order = 'concat(cl.name_first,cl.name_last)';$dir=$_REQUEST['client_order'];}
		elseif (isset($_REQUEST['author_order']))
			{$order = 'concat(au.name_first,au.name_last)';$dir=$_REQUEST['author_order'];}
		$q = '
			SELECT ap.*, cl.name_first as clf, cl.name_last as cll, cl.id_client, au.name_first as auf, au.name_last as aul
			FROM lcm_app as ap 
			LEFT JOIN lcm_case_client_org as cco on cco.id_case = ap.id_case
			LEFT JOIN lcm_client as cl on cl.id_client = cco.id_client
			LEFT JOIN lcm_author as au on au.id_author = ap.id_author
			WHERE ap.title="post-it" and dismissed = true
			ORDER BY '.$order.' '.$dir.'
			';
		$result = lcm_query($q);
		// Get the number of rows in the result
		$number_of_rows = lcm_num_rows($result);
		if ($number_of_rows) {
			$headers = array( 
					array( 'title' => 'Client', 'order' => 'client_order'),
					array( 'title' => 'Author', 'order' => 'author_order'),
					array( 'title' => 'Creation Date', 'order' => 'date_order', 'default' => 'DESC'),
					array( 'title' => 'Descrption', 'order' => 'no_order'),
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
					. $row['clf'] . ' ' . $row['cll'] . '</td>';
				echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
					. $row['auf'] . ' ' . $row['aul'] . '</td>';
				echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
					. format_date($row['date_creation'], 'date_short') . '</td>';
				echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
					. $row['description'] . '</td>';
//				echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
//					. (!$row['dismissed'] ? 'Outstanding' : 'Dismissed') . '</td>';
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
		break;
	}








matt_page_end();
lcm_page_end();

?>
