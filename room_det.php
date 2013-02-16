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

	$Id: author_det.php,v 1.35 2006/09/15 16:05:26 mlutfy Exp $
*/

include('inc/inc.php');
include_lcm('inc_contacts');
include_lcm('inc_acc');

$admin = false;
if ($GLOBALS['author_session']['right5']==1)
	{
	$admin = true;
	}

function get_date_range_fields() {
	$ret = array();

	$link = new Link();
	$link->delVar('date_start_day');
	$link->delVar('date_start_month');
	$link->delVar('date_start_year');
	$link->delVar('date_end_day');
	$link->delVar('date_end_month');
	$link->delVar('date_end_year');
	$ret['html'] =  $link->getForm();

	// By default, show from "now() - 1 month" to NOW().
	// Unlike in case_details, we cannot show all, since it would return
	// too many results.
	$ret['html'] .= "<p class=\"normal_text\">\n";
	$ret['date_end'] = get_datetime_from_array($_REQUEST, 'date_end', 'end', "-1");

	$ret['date_start'] = get_datetime_from_array($_REQUEST, 'date_start', 'start',
					date('Y-m-d H:i:s', strtotime("-1 month" . ($ret['date_end'] != "-1" ? $ret['date_end'] : date('Y-m-d H:i:s')))));

	$ret['html'] .= _Ti('time_input_date_start');
	$ret['html'] .= get_date_inputs('date_start', $ret['date_start']);

	$ret['html'] .= _Ti('time_input_date_end');
	if ($ret['date_end'] == "-1")
		$ret['html'] .= get_date_inputs('date_end');
	else
		$ret['html'] .= get_date_inputs('date_end', $ret['date_end']);

	$ret['html'] .= ' <button name="submit" type="submit" value="submit" class="simple_form_btn">'
				. _T('button_validate') 
				. "</button>\n";

	$ret['html'] .= "</p>\n";
	$ret['html'] .= "</form>\n";

	return $ret;
}

global $prefs;
global $author_session;

$room = intval(_request('id_room'));

if (! ($room > 0)) {
	lcm_header("Location: listrooms.php");
	exit;
}

// Get author data
$q= 'SELECT 
		r.*, 
		p.id_placement as pid, p.id_case as pca, p.date_start as pds, p.date_end as pde, p.status as pst,
		a.id_placement as aid, a.id_case as aca, a.date_start as ads, a.date_end as ade, a.status as ast,
		auth.name_first, auth.name_last, id_author
	from lcm_room as r 
	left join (select * from lcm_placement where status="active")as a on r.id_room = a.id_room
	left join (select * from lcm_placement where status="provisional") as p on r.id_room = p.id_room
	left join lcm_author as auth on auth.id_author = r.host
	WHERE r.id_room ='. $room;
$result = lcm_query($q);
$row = lcm_fetch_array($result);

lcm_page_start('Room Details');
matt_page_start('Room Details');

no_tabs();


show_page_subtitle(_T('generic_subtitle_general'));

echo '<ul class="info">';
echo '<li>Room ID: ' . $row['id_room'] . "</li>\n";
echo '<li>Name: <strong>' . $row['name'] . "</strong></li>\n";
echo '<li>Type: <strong>' . $row['type'] . "</strong></li>\n";
echo '<li>Gender: <strong>' . $row['sex'] . "</strong></li>\n";
if ($row['note'])
	echo '<li>Notes: <strong>' . $row['note'] . "</strong></li>\n";

echo "</ul>\n";

//
// Show 'edit author' button, if allowed
//


echo'<p class="normal_text">';
if ($admin)
	{
	if (!$row['pid'])
		{
		echo '<a href="edit_fu.php?room='.$row['id_room'].'&ctype=accomidation&type=stage_change&stage=reserved&ref=listrooms.php" class="add_lnk">
			Reserve for List Client</a>';
		echo '<a href="edit_fu.php?room='.$row['id_room'].'&ctype=accomidation&type=stage_change&stage=accomreserved&ref=listrooms.php" class="add_lnk">
			Reserve for Housed Client</a>';
		if (!$row['aid'] && $row['status']=='normal')
			{
			echo '<a href="edit_fu.php?room='.$row['id_room'].'&ctype=accomidation&type=stage_change&stage=accom&ref=listrooms.php" class="add_lnk">
				Move a Client In</a>';
			}
		}
	else
		{
		echo'<a href="edit_fu.php?room='.$row['id_room'].'&case='.$row['pca'].'&ctype=accomidation&type=stage_change&stage=unreserved&ref=listrooms.php" class="add_lnk">
			Cancel Reservation</a>';
			}
	if (!$row['aid'] && $row['status']=='normal' && $row['pid'])
		{
		echo '<a href="edit_fu.php?room='.$row['id_room'].'&case='.$row['pca'].'&ctype=accomidation&type=stage_change&stage=accom&ref=listrooms.php" class="add_lnk">
			Move In Reservation</a>';
		}
	echo '<a href="edit_room.php?room=' . $room . '" class="edit_lnk">Edit Room Details</a>';
	}
echo '</p>';

show_page_subtitle('Room History');
$q = "select p.*, cl.id_client as clid, cl.name_first, cl.name_last, cl.name_middle from lcm_placement as p left join lcm_case_client_org as cco on cco.id_case = p.id_case left join lcm_client as cl on cl.id_client = cco.id_client where p.id_room = ".$room." order by p.date_start desc";
$result = lcm_query($q);
$number_of_rows = lcm_num_rows($result);

echo '<ul class="info">';
while ($row1 = lcm_fetch_array($result))
	{
	echo '<li>';
	if ($row1['status'] =='active')
		{
		echo 'Currently occupied by <b>'.get_person_name($row1).'</b> from <b>'. format_date($row1['date_start'],date_short).'</b>.';
		}
	elseif ($row1['status'] =='terminated')
		{
		echo 'Occupied by <b>'.get_person_name($row1).'</b> from <b>'. format_date($row1['date_start'],date_short).'</b> until <b>'.format_date($row1['date_end'],date_short).'</b>.';
		}
	elseif ($row1['status'] =='provisional')
		{
		echo 'Reserved for <b>'.get_person_name($row1).'</b> on <b>'. format_date($row1['date_start'],date_short).'</b>.';
		}
	elseif ($row1['status'] =='declined')
		{
		echo 'Reserved for <b>'.get_person_name($row1).'</b> on <b>'. format_date($row1['date_start'],date_short).'</b>. Reservation canceled on <b>'.format_date($row1['date_end'],date_short).'</b>.';
		}
	else
		echo $row1['status'];
	echo '</li>';
	}
echo '</ul>';






/*
if (isset($_REQUEST['list_pos']))
	$list_pos = $_REQUEST['list_pos'];
else
	$list_pos = 0;
if ($list_pos>=$number_of_rows) $list_pos = 0;
if ($list_pos>0)
	if (!lcm_data_seek($result,$list_pos))
		lcm_panic("Error seeking position $list_pos in the result");
$headers = array();
$headers[0]['title'] = 'Type';
$headers[1]['title'] = 'From';
$headers[2]['title'] = 'Until';
$headers[3]['title'] = 'Client';
$headers[4]['title'] = 'Options';
show_list_start($headers);
$prov=false;
$activ=false;
for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) 
	{
	echo "<tr>\n";
	echo "<td width = '15%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo $row['status'];
	echo "</td>";
	echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo ($row['date_start']?format_date($row['date_start'],'date_short'):'---');
	echo "</td>";
	echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo ($row['date_end']?format_date($row['date_end'],'date_short'):'---');
	echo "</td>";
	echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo get_person_name($row);
	echo "</td>";
	echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	if ($row['status']=='provisional')
		{
		$prov=$row['id_placement'];
		echo 'Move In';
		echo ' | ';
		echo 'Decline';
		}
	if ($row['status']=='active')
		{
		$activ=$row['id_placement'];
		if ($row['date_end']=='') {;$unendactiv=true;}
		echo 'Terminate';
		echo ' | ';
		echo 'Edit';
		}
	echo "</td>";
	echo "</tr>";
	}
show_list_end();
echo '<p class="normal_text">';
if ((!$prov) && (!$unendactiv))
	{
	echo '<a href="edit_placement.php?type=provisional&room='.$room.'" class="edit_lnk">Reserve this Room</a>';
	}

if ($prov)
	{
	echo '<a href="edit_placement.php?placement='.$prov.'&type=active" class="edit_lnk">Move in Reserved Client</a>';
	echo '<a href="edit_placement.php?placement='.$prov.'&type=declined" class="edit_lnk">Cancel Reservation</a>';
	}

if ($activ)
	{
	echo '<a href="edit_placement.php?placement='.$activ.'&type=active" class="edit_lnk">Edit Placement</a>';
	}
echo '</p>';

/*






























/*
$result = lcm_query($q);
echo '<table class="tbl_usr_dtl">';
$i=0;
while ($row = lcm_fetch_array($result))
	{
	$i++;
	echo '<tr>';
	echo '<td class="tbl_cont_dark" >'.$row['date_start'].'</td>';
	echo '<td>'. ($row['date_end']?$row['date_end']:'Ongoing').'</td>';
	echo '<td><a class="content_lnk" href="client_det.php?client='. $row['id_client'] . '">'.$row['name_first'].' '.$row['name_last'].'</a></td>';
	echo '</tr>';
	}
echo '</table>';
*/




//				break;
/*			//
			// Cases tab
			//
			case 'cases':
				include_lcm('inc_obj_case');

				// Note: If the user is looking at his/her cases, then list only those
				// If a user is looking at another users's cases, then list only public cases
				// If the admin is looking at another users's cases, then show all
				show_page_subtitle(_T('author_subtitle_cases', array('author' => get_person_name($author_data)), 'cases_participants'));

				$foo = get_date_range_fields();
				echo $foo['html'];

				$case_list = new LcmCaseListUI();

				if (($find_case_string = _request('find_case_string')))
					$case_list->setSearchTerm($find_case_string); // There is no UI for this at the moment XXX test

				$case_list->setDateInterval($foo['date_start'], $foo['date_end']);
				$case_list->setDataInt('id_author', $author);
				$case_list->setDataString('owner', 'my');

				$case_list->start();
				$case_list->printList();
				$case_list->finish();

				break;
			//
			// Author followups
			//
			case 'followups':
				if (! allowed_author($author, 'r'))
					die("Access denied");
			
				show_page_subtitle(_T('author_subtitle_followups', array('author' => get_person_name($author_data))), 'cases_followups');

				$foo = get_date_range_fields();

				$date_start = $foo['date_start'];
				$date_end   = $foo['date_end'];

				echo $foo['html'];

				echo "<p class=\"normal_text\">\n";
				show_listfu_start('author');
			
				$q = "SELECT id_followup, id_case, date_start, date_end, type, description, case_stage, hidden
					FROM lcm_followup
					WHERE id_author = $author
					  AND UNIX_TIMESTAMP(date_start) >= UNIX_TIMESTAMP('" .  $date_start . "') ";

				if ($date_end != "-1")
					$q .= " AND UNIX_TIMESTAMP(date_start) <= UNIX_TIMESTAMP('" . $date_end . "')";
			
				// Add ordering
				if ($fu_order)
					$q .= " ORDER BY date_start $fu_order, id_followup $fu_order";
			
				$result = lcm_query($q);

				// Check for correct start position of the list
				$number_of_rows = lcm_num_rows($result);
				$list_pos = 0;
				
				if (isset($_REQUEST['list_pos']))
					$list_pos = $_REQUEST['list_pos'];

				if (is_numeric($list_pos)) {
					if ($list_pos >= $number_of_rows)
						$list_pos = 0;
				
					// Position to the page info start
					if ($list_pos > 0)
						if (!lcm_data_seek($result,$list_pos))
							lcm_panic("Error seeking position $list_pos in the result");
				
					$show_all = false;
				} elseif ($list_pos == 'all') {
					$show_all = true;
				}
			
				// Process the output of the query
				// [ML] I don't know if I'm drinking too much coffee, but "$list_pos == 'all'" would always return 1
				for ($i = 0; (($i < $prefs['page_rows']) || $show_all) && ($row = lcm_fetch_array($result)); $i++)
					show_listfu_item($row, $i, 'author');

				show_list_end($list_pos, $number_of_rows, true);
				echo "</p>\n";

				// Total hours for period
				$q = "SELECT sum(IF(UNIX_TIMESTAMP(date_end) > UNIX_TIMESTAMP(date_start), 
								UNIX_TIMESTAMP(date_end)-UNIX_TIMESTAMP(date_start), 0)) as total_time
					FROM lcm_followup
					WHERE id_author = $author
					  AND UNIX_TIMESTAMP(date_start) >= UNIX_TIMESTAMP('" .  $date_start . "') ";

				if ($date_end != "-1")
					$q .= " AND UNIX_TIMESTAMP(date_end) <= UNIX_TIMESTAMP('" . $date_end . "')";
				
				$q .= "	GROUP BY id_author";

				$result = lcm_query($q);
				$row = lcm_fetch_array($result);
				
				echo '<p class="normal_text">';
				echo _Ti('generic_input_total')
					. format_time_interval($row['total_time'], true)
					. " " . _T('time_info_short_hour')
					. "<br />\n";
				echo "</p>\n";
				
				break;
			//
			// Time spent on case by authors
			//
			case 'times' :
				if (! allowed_author($author, 'r'))
					die("Access denied");

				// List time spent for each case
				// Show table headers
				show_page_subtitle(_T('author_subtitle_reports', array('author' => get_person_name($author_data))), 'reports_intro');

				function show_report_for_user($author, $date_start, $date_end, $type) {
					if ($type == "case") {
						$q = "SELECT c.title, c.id_case, 
								sum(IF(UNIX_TIMESTAMP(fu.date_end) > 0,
									UNIX_TIMESTAMP(fu.date_end)-UNIX_TIMESTAMP(fu.date_start), 0)) as time,
								sum(sumbilled) as sumbilled 
						 	  FROM lcm_case as c, lcm_followup as fu 
							  WHERE fu.id_case = c.id_case AND fu.id_author = $author
								AND UNIX_TIMESTAMP(date_start) >= UNIX_TIMESTAMP('" . $date_start . "') ";

						if ($date_end != "-1") 
							$q .= " AND UNIX_TIMESTAMP(date_end) <= UNIX_TIMESTAMP('" . $date_end . "')";

						$q .= " GROUP BY fu.id_case";
					} elseif ($type == "fu") {
						$q = "SELECT fu.type,
								sum(IF(UNIX_TIMESTAMP(fu.date_end) > 0,
									UNIX_TIMESTAMP(fu.date_end)-UNIX_TIMESTAMP(fu.date_start), 0)) as time,
								sum(sumbilled) as sumbilled 
						 	  FROM lcm_followup as fu 
							  WHERE fu.id_author = $author
								AND UNIX_TIMESTAMP(date_start) >= UNIX_TIMESTAMP('" . $date_start . "') ";

						if ($date_end != "-1")
							$q .= " AND UNIX_TIMESTAMP(date_end) <= UNIX_TIMESTAMP('" . $date_end . "') ";

						$q .= " GROUP BY fu.type";
					} elseif ($type == "agenda") {
						$q = "SELECT ap.type,
								sum(IF(UNIX_TIMESTAMP(ap.end_time) > 0,
									UNIX_TIMESTAMP(ap.end_time)-UNIX_TIMESTAMP(ap.start_time), 0)) as time
						 	  FROM lcm_app as ap
							  WHERE ap.id_author = $author
							  	AND ap.id_case = 0
								AND UNIX_TIMESTAMP(start_time) >= UNIX_TIMESTAMP('" . $date_start . "') ";

						if ($date_end != "-1")
							$q .= " AND UNIX_TIMESTAMP(end_time) <= UNIX_TIMESTAMP('" . $date_end . "') ";

						$q .= " GROUP BY ap.type";
					}

					$result = lcm_query($q);

					echo "<p class=\"normal_text\">\n";
					echo "<table border='0' class='tbl_usr_dtl' width='99%'>\n";
					echo "<tr>\n";

					echo '<th class="heading">'
						. _T('case_subtitle_times_by_' . $type)
						. "</th>\n";

					echo "<th class='heading' width='1%' nowrap='nowrap'>" 
						. _Th('case_input_total_time') . ' (' . _T('time_info_short_hour') . ")"
						. "</th>\n";

					$total_time = 0;
					$total_sum_billed = 0.0;

					$meta_sum_billed = (read_meta('fu_sum_billed') == 'yes');
					$meta_sum_billed &= ($type == "case" || $type == "fu");

					if ($meta_sum_billed) {
						$currency = read_meta('currency');
						echo "<th class='heading' width='1%' nowrap='nowrap'>" . _Th('fu_input_sum_billed') . ' (' . $currency . ")</th>\n";
					}

					echo "</tr>\n";

					// Show table contents & calculate total
					while ($row = lcm_fetch_array($result)) {
						echo "<tr>\n";
						echo "<!-- Total = " . $total_sum_billed . " - row = " . $row['sumbilled'] . " -->\n";
	
						$total_time += $row['time'];
						$total_sum_billed += $row['sumbilled'];
	
						echo '<td>';
						
						if ($type == "case") {
							echo '<a class="content_link" href="case_det.php?case=' . $row['id_case'] . '">'
								. $row['id_case'] . ': '
								.  $row['title'] 
								. '</a>';
						} elseif ($type == "fu") {
							echo  _Tkw("followups", $row['type']);
						} elseif ($type == "agenda") {
							echo _Tkw("appointments", $row['type']);
						}
						
						echo '</td>';

						echo '<td align="right">'
						. format_time_interval_prefs($row['time'])
						. "</td>\n";
	
						if ($meta_sum_billed) {
							echo '<td align="right">';
							echo format_money($row['sumbilled']);
							echo "</td>\n";
						}
	
						echo "</tr>\n";
					}

					// Show total case hours
					echo "<tr>\n";
					echo "<td><strong>" . _Ti('generic_input_total') . "</strong></td>\n";
					echo "<td align='right'><strong>";
					echo format_time_interval_prefs($total_time);
					echo "</strong></td>\n";

					if ($meta_sum_billed) {
						echo '<td align="right"><strong>';
						echo format_money($total_sum_billed);
						echo "</strong></td>\n";
					}

					echo "</tr>\n";
					echo "</table>\n";
					echo "</p>\n";
				}
				
				$foo = get_date_range_fields();
				echo $foo['html'];

				show_report_for_user($author, $foo['date_start'], $foo['date_end'], 'case');
				show_report_for_user($author, $foo['date_start'], $foo['date_end'], 'fu');
//				show_report_for_user($author, $foo['date_start'], $foo['date_end'], 'agenda');

				break;

			case 'appointments':
				if (! allowed_author($author, 'r'))
					die("Access denied");

				show_page_subtitle(_T('author_subtitle_appointments', array('author' => get_person_name($author_data))), 'tools_agenda');

				$foo = get_date_range_fields();

				$date_start = $foo['date_start'];
				$date_end   = $foo['date_end'];

				echo $foo['html'];

				echo "<p class=\"normal_text\">\n";

				$q = "SELECT ap.*
					FROM lcm_author_app as aa, lcm_app as ap
					WHERE aa.id_app = ap.id_app
						AND UNIX_TIMESTAMP(start_time) >= UNIX_TIMESTAMP('" . $date_start . "') ";

				if ($date_end != "-1") 
					$q .= " AND UNIX_TIMESTAMP(end_time) <= UNIX_TIMESTAMP('" . $date_end . "') ";

//				$q .= " AND aa.id_author = " . $GLOBALS['author_session']['id_author'];
				$q .= " AND aa.id_author = " . $author_data['id_author']. " ";				
				// Sort agenda by date/time of the appointments
				$order = 'DESC';
				if (isset($_REQUEST['order']))
					if ($_REQUEST['order'] == 'ASC' || $_REQUEST['order'] == 'DESC')
						$order = $_REQUEST['order'];
				
				$q .= " ORDER BY start_time " . $order;
				$result = lcm_query($q);
				// Get the number of rows in the result
				$number_of_rows = lcm_num_rows($result);
				if ($number_of_rows) {
					$headers = array( array( 'title' => 'Date', 'order' => 'order', 'default' => 'DESC'),
							array( 'title' => 'Status', 'order' => 'no_order'),
//							array( 'title' => _Th('app_input_type'), 'order' => 'no_order'),
							array( 'title' => _Th('app_input_title'), 'order' => 'no_order'));
							// array( 'title' => _Th('app_input_reminder'), 'order' => 'no_order'));
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
							. format_date($row['start_time'], 'date_short') . '</td>';
				
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
							. (!$row['dismissed'] ? 'Outstanding' : 'Dismissed') . '</td>';
//						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . _Tkw('appointments', $row['type']) . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
							. '<a href="app_det.php?app=' . $row['id_app'] . '" class="content_link">' . $row['title'] . '</a></td>';
						// [ML] removed, not very useful.
						// echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
						//	. format_date($row['reminder'], 'short') . '</td>';
						echo "</tr>\n";
					}
				
					show_list_end($list_pos, $number_of_rows);
				}
				
				echo "</p>\n";
//MATT WAS HERE. CANNOT CREATE FTUTURE DATE OUTSIDE OF A CASE
//				if ($author_session['id_author'] == $author)
//					echo '<p><a href="edit_app.php?app=0" class="create_new_lnk">' . _T('app_button_new') . '</a></p>';

				break;
	
			//
			// Case attachments
			//
			case 'attachments' :
				show_author_attachments($author);

				break;*/
//		}

echo "</fieldset>\n";
lcm_page_end();

?>
