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


if ($GLOBALS['author_session']['right3']==1)
	{
	$admin = true;
	}
lcm_page_start('Administrator');
matt_page_start('Administrator');
$groups = array(
	'outstanding' => 'Post-its',
//	'dismissed' => 'Dismissed',
	);
$tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'outstanding' );
show_tabs($groups,$tab,$_SERVER['REQUEST_URI']);
no_tabs();

switch ($tab)
	{
	case 'outstanding':
		show_notes('post-admin','admin.php',$admin);
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
