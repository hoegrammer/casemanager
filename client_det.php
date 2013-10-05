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
include_once('inc/DataRetrieval.class.php');

include('inc/SupportCombo.class.php');
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
lcm_page_start('File for Client: '. get_person_name($row), '', '', 'clients_intro');
matt_page_start('File for '.get_person_name($row));
$edit = true;

// Show tabs
$groups = array(
	'work' => 'File',
	'details' => 'Details',
	'documents'=> 'Documents',
	'key_dates' => 'Key Dates'
	);
$tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'work' );
show_tabs($groups,$tab,$_SERVER['REQUEST_URI']);


$admin=false;
$accad=false;
$supad=false;
if($author_session['right3']) $admin=true;
if($author_session['right4']) $supad=true;
if($author_session['right5']) $accad=true;

$obj_client = new LcmClientInfoUI($client);
switch ($tab) {
	case 'details':
		
		$record = ( isset($_GET['record']) ? $_GET['record'] : '0' );
		echo "<form action='client_det.php' method='get'><p>";
		echo "<select name='record'>";
		print "<option value=''>".format_date($obj_client->getDataString('date_update'),'date_short')." (Current)</option>";
		$result=lcm_query('select * from lcm_old_client where id_client='.$client.' ORDER BY date_update DESC');
		while( $row = lcm_fetch_array($result))
			{
			print "<option".($row['id_record']==$record?' selected ':' ')."value='".$row['id_record']."'>".format_date($row['date_update'], 'date_short')."</option>";
			}
		echo "</select>  ";
		echo "<input type='hidden' name='tab' value='details'/>";
		echo "<input type='hidden' name='client' value='".$client."'/>";
		echo "<button type='submit' class='simple_form_btn'>Submit</button></p>";
		echo "</form>";
		
		if ($record >0)
			{
			$obj_record = new LcmClientInfoUI($client,$record);
			$obj_record->printGeneral();
			}
		else
			{
			$obj_client->printGeneral();
			}
		
		if ($GLOBALS['author_session']['right2']==1)
			{
			echo '<p><a href="edit_client.php?client=' .$client . '" class="edit_lnk">' .  _T('client_button_edit') . '</a></p>';
			}
		break;
	case 'dates':
		echo "<p class=\"normal_text\">\n";

		$q = "
			SELECT ap.*, cl.name_first, cl.name_last, c.type_case
			FROM lcm_app as ap 
			LEFT JOIN lcm_case as c on c.id_case = ap.id_case
			LEFT JOIN lcm_case_client_org as cco on c.id_case = cco.id_case
			LEFT JOIN lcm_client as cl on cl.id_client = cco.id_client
			WHERE 1=1
			AND cl.id_client=".$client."
			";

	
		$q .= " ORDER BY start_time " . $order;
		$result = lcm_query($q);
		// Get the number of rows in the result
		$number_of_rows = lcm_num_rows($result);
		if ($number_of_rows) {
			$headers = array( 
					array( 'title' => 'Event', 'order' => 'no_order'),
					array( 'title' => 'Date', 'order' => 'no_order', 'default'=>'DESC'),
					array( 'title' => 'Creation Date', 'order' => 'no_order'),
					array( 'title' => 'Status', 'order' => 'no_order'),
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
					. $row['title'] . '</td>';
				echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
					. format_date($row['start_time'], 'date_short') . '</td>';	
				echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
					. format_date($row['date_creation'], 'date_short') . '</td>';
				echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">'
					. (!$row['dismissed'] ? 'Outstanding' : 'Dismissed') . '</td>';
				echo "</tr>\n";
			}
		
			show_list_end($list_pos, $number_of_rows);
		}
		
		echo "</p>\n";
		break;
	case 'work':
		$q = "SELECT a.start_time, a.title as ttl, clo.id_case, kw.title, c.date_update, c.date_creation, c.status, c.stage, c.type_case, c.amount, c.legal_reason
			FROM lcm_case_client_org as clo, lcm_case as c
			NATURAL LEFT JOIN lcm_keyword_case as kc 
			LEFT JOIN lcm_keyword as kw ON kc.id_keyword = kw.id_keyword
			LEFT JOIN 
				(
				select a.* from lcm_app as a where a.dismissed = false and (title='suprev' or title='supterm' or title='accrev' or title = 'accterm' or title='salrev')
				) as a on a.id_case = c.id_case

			WHERE id_client = " . $client . "
			AND clo.id_case = c.id_case  
			AND c.type_case != 'Default'";
		// Sort cases by creation date
		$case_order = 'DESC';
		if (isset($_REQUEST['case_order']))
			if ($_REQUEST['case_order'] == 'ASC' || $_REQUEST['case_order'] == 'DESC')
				$case_order = $_REQUEST['case_order'];
		
		$q .= " ORDER BY c.status " . $case_order;

		$result = lcm_query($q);
		$number_of_rows = lcm_num_rows($result);
		$list_pos = 0;
		
		if (isset($_REQUEST['list_pos']))
			$list_pos = $_REQUEST['list_pos'];
		
		if ($list_pos >= $number_of_rows)
			$list_pos = 0;
		
		// Position to the page info start
		if ($list_pos > 0)
			if (!lcm_data_seek($result,$list_pos))
				lcm_panic("Error seeking position $list_pos in the result");
		
		$acc_stage = false;
		$sup_stage = false;
		
		// include code that decides if client is currently supported and if so 
		// presents FAO Welfare Desk panel
		require 'inc/fao_welfare_desk.php';	
		require 'inc/accompanied_by.php';	

		echo "<table class='table_strands'>";
		for ($cpt = 0; (($i<$prefs['page_rows']) && ($row1 = lcm_fetch_array($result))); $cpt++)
		
			{
			if (!$cpt % 2)
				{
				echo "<tr>";
				}
			echo "<td class='td_strand'><small>";
			echo "<div class='td_strand_title'>";
			if ($row1['type_case']=='Accomidation')
				echo "Accommodation";
			elseif ($row1['type_case']=='Support')
				echo "Support";
			elseif ($row1['type_case']=='Befriender')
				echo "Befriender";
			else
				echo "Other";

			echo "</div>";
			echo "<div class='td_strand_".($row1['status']=='open'?"inner":"gray")."'>";
			echo "Status: ";
				
			$kws= get_keywords_in_group_name('stage');
			foreach ($kws as $kw)
				{
				if ($kw['name']==$row1['stage'])
					{
					echo "<b>". $kw['title'] . "</b>";;
					}
				}
			echo "<br />";
			
			if ($row1['stage']=='accom')
				{
				$qq = "select r.id_room, r.name from lcm_placement as p left join lcm_room as r on p.id_room = r.id_room where p.id_case = ".$row1['id_case']." and p.status='active'"; 
				$rr = lcm_fetch_array(lcm_query($qq));
				if ($rr['id_room'])
					{
					echo "Accommodated in: ";
					echo "<a class='content_link' href='room_det.php?id_room=".$rr['id_room']."'>".$rr['name']."</a>";
					echo "<br />";
					}
				}

			if ((($row1['amount']>0)||($row1['legal_reason']=='yes')) && $row1['status']=='open')
				{
				echo "Weekly Support: £".$row1['amount'].($row1['legal_reason']=='yes'?' & a bus pass':'');
				echo "<br />";
				}
			switch ($row1['stage'])
				{
				case 'submitted':
					$text='Submitted on';
					$futype='followups17" or type="followups28';
					break;
				case 'supported':
					$text='Supported on';
					$futype='followups20';
					break;
				case 'accom':
					$text='Moved in on';
					$futype='followups24';
					break;
				case 'terminated':
					$text='Terminated on';
					$futype='followups22';
					if ($row1['type_case'] == 'Accomidation') {
						$moved_in = lcm_fetch_array(
							lcm_query(
								"select date_format(date_start, '%e %b %Y') from lcm_followup where id_case = ". $row1['id_case'] .
								" and type = 'followups24' and date_start < '" . $row1['date_update']. "' order by date_start 
								desc limit 1"
							)
						);
						echo "<b>MOVED IN ON</b>: ". $moved_in[0] . "<br />";
					} else {
						$supported_on = lcm_fetch_array(                                                        
							lcm_query(
                                                                "select date_format(date_start, '%e %b %Y') from lcm_followup where id_case = ". $row1['id_case'] .
                                                                " and type = 'followups20' and date_start < '" . $row1['date_update']. "' order by date_start 
                                                                desc limit 1"
                                                        )
                                                );
						echo "<b>SUPPORT STARTED ON</b>: ". $supported_on[0] . "<br />";

					}
					
					break;
				case 'rejected':
					$text='Rejected on';
					$futype='followups19';
					break;
				case 'waiting list':
					$text='Added to waiting list on';
					$futype='followups18';
					break;
				default :
					$text='First Submitted on';
					$futype='followups17';
					break;
				}
		
			$row2=lcm_fetch_array(lcm_query('select max(date_start) as date_start from lcm_followup where id_case='.$row1['id_case'].' and type="'.$futype.'"'));
			echo $text . ": " . format_date($row2['date_start'], 'date_short') . "<br />";

			if ($row1['start_time'])
				{
				echo "<b>";
				if ($row1['ttl']=='salrev')
					echo "SAL Review Due: ";
				elseif ($row1['ttl']=='suprev')
					echo "Next Panel Review Due: ";
				elseif ($row1['ttl']=='accrev')
					echo "Next Accomidation Review Due: ";
				elseif ($row1['ttl']=='supterm'||$row1['ttl']=='accterm')
					echo "Termination Due: ";
				else
					echo "Error! Date: ";
				echo format_date($row1['start_time'], 'date_short') . "</b><br />";

				}


			// actions
			echo "Actions: ";
			$ref="client_det.php?client=".$id_client;
			if ($row1['status']=='open')
				{
				if ($row1['type_case']=='Befriender')
					{
					if ($row1['stage']=='submitted')
						{
						echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=befriender&type=stage_change&stage=rejected&'.$ref.'" class="content_link">Reject</a>&nbsp';
						}
					if ($row1['stage']=='bef')
						{
						echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=befriender&type=stage_change&stage=terminated&'.$ref.'" class="content_link">Terminate</a>&nbsp';
						}
					}

				if ($row1['type_case']=='Support')
					{
					if ((($row1['stage']=='submitted')||($row1['stage']=='submitted2')||($row1['stage']=='submitted3')||($row1['stage']=='submitted4'))
						&&($supad))
						{
						echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=support&type=stage_change&stage=supported&'.$ref.'" class="content_link">Support</a>&nbsp';
//						echo ' | ';
						echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=support&type=stage_change&stage=rejected&'.$ref.'" class="content_link">Reject</a>&nbsp';
						}
					if (($row1['stage']=='submitted')&&($supad))	
						{
						echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=support&type=stage_change&stage=submitted2&'.$ref.'" class="content_link">Defer</a>&nbsp';
						}
					elseif (($row1['stage']=='submitted2')&&($supad))	
						{
						echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=support&type=stage_change&stage=submitted3&'.$ref.'" class="content_link">Defer</a>&nbsp';
						}
					elseif (($row1['stage']=='submitted3')&&($supad))	
						{
						echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=support&type=stage_change&stage=submitted4&'.$ref.'" class="content_link">Defer</a>&nbsp';
						}
					elseif ($row1['stage']=='supported')
						{
						if ($supad)
							{
							echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=support&type=review&'.$ref.'" class="content_link">Review Now</a>&nbsp';
							}
						if (($row1['ttl']=='salrev')&&($GLOBALS['author_session']['right6']))
							{
							echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=support&type=salreview&'.$ref.'" class="content_link">SAL Review Now</a>&nbsp';
							}
						if (($row1['ttl']=='supterm'||$row1['ttl']=='accterm')&&($supad))
							{
							echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=support&type=stage_change&stage=terminated&'.$ref.'" class="content_link">Terminate Now</a>&nbsp';
							}
						}
					elseif ($row1['stage']=='supported_notice')
						{
						echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=support&type=stage_change&stage=terminated&'.$ref.'" class="content_link">
								This should not exist
								</a>&nbsp';
						}
					}
				elseif ($row1['type_case']=='Accomidation')
					{
					if ($row1['stage']=='submitted')
						{
						if ($accad)
							{
							echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=accomidation&type=stage_change&stage=waiting list&'.$ref.'" 
								class="content_link">Add to waiting list</a>&nbsp';
							echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=accomidation&type=stage_change&stage=rejected&'.$ref.'" class="content_link">Reject</a>&nbsp';
							}
						if ($GLOBALS['author_session']['right2']==1)
							{
							echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=accomidation&type=followups28&'.$ref.'" class="content_link">Resubmit</a>&nbsp';
							}
						}
					elseif (($row1['stage']=='waiting list')&&($accad))
						{
//						if ($admin)
//							{
//							echo '<a href="listrooms.php?'.$ref.'" class="content_link">Find Room</a>';
//							echo ' | ';
//							}
						echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=accomidation&type=stage_change&stage=rejected&'.$ref.'" class="content_link">Reject</a>&nbsp';
						}
					elseif ($row1['stage']=='accom'|| $row1['stage']=='accomreserved')
						{
						if ($accad)
							{
							echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=accomidation&type=review&'.$ref.'" class="content_link">Review Now</a>&nbsp';
							}
						if (($row1['ttl']=='salrev')&&($GLOBALS['author_session']['right6']))
							{
							echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=accomidation&type=salreview&'.$ref.'" class="content_link">SAL Review Now</a>&nbsp';
							}
						if ($row1['ttl']=='supterm'||$row1['ttl']=='accterm')
							{
							echo '<a href="edit_fu.php?case='.$row1['id_case'].'&ctype=accomidation&type=stage_change&stage=terminated&'.$ref.'" class="content_link">Terminate Now</a>&nbsp';
							}
						if ($row1['stage']=='accomreserved')
							{
							$placement=lcm_fetch_array(lcm_query("select * from lcm_placement where status='provisional' and id_case=".$row1['id_case'])); 
							if ($placement['id_room'])
								{
								$active=lcm_fetch_array(lcm_query("select * from lcm_placement where status='active' and id_room=".$placement['id_room'])); 
								echo '<a href="edit_fu.php?room='.$placement['id_room'].'&case='.$row1['id_case'].'&ctype=accomidation&type=stage_change&stage=unreserved2&ref=client_det.php?client='.$client.'" class="content_link">Cancel Reservation</a>&nbsp';
								if (!$active)
									{
									echo '<a href="edit_fu.php?room='.$placement['id_room'].'&case='.$row1['id_case'].'&ctype=accomidation&type=stage_change&stage=accom&ref=client_det.php?client='.$client.'" class="content_link">Move into new Room</a>&nbsp';
									}
								}
							}
						}
					elseif ($row1['stage']=='reserved')
						{
						$placement=lcm_fetch_array(lcm_query("select * from lcm_placement where status='provisional' and id_case=".$row1['id_case'])); 
						if ($placement['id_room'])
							{
							$active=lcm_fetch_array(lcm_query("select * from lcm_placement where status='active' and id_room=".$placement['id_room'])); 
							echo '<a href="edit_fu.php?room='.$placement['id_room'].'&case='.$row1['id_case'].'&ctype=accomidation&type=stage_change&stage=unreserved&ref=client_det.php?client='.$client.'" class="content_link">Cancel Reservation</a>&nbsp';
							if (!$active)
								{
								echo '<a href="edit_fu.php?room='.$placement['id_room'].'&case='.$row1['id_case'].'&ctype=accomidation&type=stage_change&stage=accom&ref=client_det.php?client='.$client.'" class="content_link">Move In Reservation</a>&nbsp';
								}
							}
						}
					}
				}
			if ($row1['status']=='open')
				{
				if ($row1['type_case']=='Support')
					{
					$sup_stage=$row1['stage'];
					}
				if ($row1['type_case']=='Accomidation')
					{
					$acc_stage=$row1['stage'];
					}
				if ($row1['type_case']=='Befriender')
					{
					$bef_stage=$row1['stage'];
					}
				if ($row1['type_case']=='Section4')
					{
					$s4_stage=$row1['stage'];
					}
				}
			echo "</small></div></td>";
			if ($cpt % 2)
				{
				echo "</tr>";
				}
			}	

		//AND NEW STRAND CODE
		if (!$cpt % 2)
			{
			echo "<tr>";
			}
		echo "<td class='td_strand'><small>";
		echo "<div class='td_strand_title'>Create New Strand...</div>";
		if ($GLOBALS['author_session']['right2']==1)
			{
			echo "<div class='td_strand_".($row1['status']=='open'?"inner":"gray")."'>";
			if (!$sup_stage && $acc_stage!='accom')
				echo "<a href=\"edit_case.php?case=0&type=support&attach_client=$client\" class=\"content_link\">Submit to Panel</a><br />";
			if (!$acc_stage)
				echo "<a href=\"edit_case.php?case=0&type=accomidation&attach_client=$client\" class=\"content_link\">Submit to Accommodation Team</a><br />\n";
			if (!$bef_stage)
				echo "<a href=\"edit_case.php?case=0&type=befriender&attach_client=$client\" class=\"content_link\">Submit for Befriender</a><br />\n";
			}
		
		echo "</small></div></td>";
		if (!$cpt % 2)
			{
			echo "<td></td>";
			}

		echo "</tr>";
		
		
		$res = lcm_query('select p.*, r.name from lcm_placement as p left join lcm_room as r on r.id_room = p.id_room left join lcm_case_client_org as cco on cco.id_case = p.id_case where cco.id_client='.$client);
		if(lcm_num_rows($res))
			{
			echo "<tr><td colspan='2' class='td_strand'>";
			echo "<div class='td_strand_title'><small>Quick Room History</small></div>";
			echo "<div class='td_strand_inner'>";
			echo "<ul class='info'>";
			while ($row1 = lcm_fetch_array($res) )
				{
				echo '<li>';
				if ($row1['status'] =='active')
					{
					echo 'Currently living in room <b>"<a class="content_link"href="room_det.php?id_room='.$row1['id_room'].'">'.$row1['name'].'</a>"</b> from <b>'. format_date($row1['date_start'],date_short).'</b>.';
					}
				elseif ($row1['status'] =='terminated')
					{
					echo 'Lived in room <b>"<a class="content_link" href="room_det.php?id_room='.$row1['id_room'].'">'.$row1['name'].'</a>"</b> from <b>'. format_date($row1['date_start'],date_short).'</b> until <b>'.format_date($row1['date_end'],date_short).'</b>.';
					}
				elseif ($row1['status'] =='provisional')
					{
					echo 'Reserved for room <b>"<a class="content_link" href="room_det.php?id_room='.$row1['id_room'].'">'.$row1['name'].'</a>"</b> on <b>'. format_date($row1['date_start'],date_short).'</b>.';
					}
				elseif ($row1['status'] =='declined')
					{
					echo 'Reserved for room <b>"<a class="content_link" href="room_det.php?id_room='.$row1['id_room'].'">'.$row1['name'].'</a>"</b> on <b>'. format_date($row1['date_start'],date_short).'</b>. Reservation canceled on <b>'.format_date($row1['date_end'],date_short).'</b>.';
					}
				else
					echo $row1['status'];
				echo '</li>';
				}
			echo '</ul></div></td></tr>';
			}
		echo "</table>";

		$show_ns=false;
		if ($GLOBALS['author_session']['right8'])
			{
			$show_ns=true;
			}
		
		$q = "SELECT c.type_case, cco.id_case 
				FROM lcm_case_client_org as cco left join lcm_case as c on cco.id_case = c.id_case 
				WHERE cco.id_client=".$client."
				AND c.type_case = 'Default'";
		$result = lcm_query($q);
		$row = lcm_fetch_array($result);
		echo '<p class="normal_text">';
		if ($GLOBALS['author_session']['right2'])
			{
			echo "<a href=\"edit_fu.php?case=".$row['id_case']."\" class=\"create_new_lnk click_btn\">" . _T('new_followup') . "</a>\n";
			echo '<a href="edit_client.php?client=' . $client . '&mode=scores" class="edit_lnk">Update Client Scores</a>' . "\n";
			echo "<a href=\"edit_fu.php?&type=followups43&case=".$row['id_case']."\" class=\"create_new_lnk click_btn\">Emergency Payment</a>\n";
			echo "<a href=\"edit_fu.php?type=followups34&case=".$row['id_case']."\" class=\"create_new_lnk click_btn\">Post-it Note</a>\n";
			}
		if ($show_ns)
			{
			echo "<a href=\"edit_fu.php?type=followups30&case=".$row['id_case']."\" class=\"create_new_lnk click_btn\">Report night at shelter</a>\n";
			}
			
		echo "<a href=\"edit_fu.php?type=followups44&case=".$row['id_case']."\" class=\"create_new_lnk click_btn\">Report night at host</a>\n";
		echo "</p>\n";
		$obj_client->printFollowups();
		if ($GLOBALS['author_session']['right3'])
			{
			echo "<p><a href=\"client_del.php?client=".$client."\" class=\"edit_lnk\">Delete Client</a></p>\n";
			}
		break;
			case 'documents' :
				echo "<p class=\"normal_text\">\n";
				echo '<form enctype="multipart/form-data" action="attach_file.php" method="post">' . "\n";
				echo '<input type="hidden" name="client" value="' . $client . '" />' . "\n";

				// List of attached files
				show_attachments_list('client', $client);

				// Attach new file form
				if ($GLOBALS['author_session']['right2'])
					{
					show_attachments_upload('client', $client);
					echo '<input type="submit" name="submit" value="' . _T('button_validate') . '" class="search_form_btn" />' . "\n";
					}
				echo "</form>\n";

				echo "</p>\n";
				break;
			case 'key_dates':
				$sql = "select 
                                                if(start = 0, '', date_format(start, '%d %M %Y') ) as start,
                                                if(first = 0, '', date_format(first, '%d %M %Y') ) as first,
                                                if(fifth = 0, '', date_format(fifth, '%d %M %Y') ) as fifth,
                                                if(ninth = 0, '', date_format(ninth, '%d %M %Y') ) as ninth,
                                                if(yearend = 0, '', date_format(yearend, '%d %M %Y') ) as yearend,
                                                if(accomend = 0, '', date_format(accomend, '%d %M %Y') ) as accomend,
                                                if(supportend = 0, '', date_format(supportend, '%d %M %Y' )) as supportend
                                                 from key_dates where id_client = " . $row['id_client'];
				$dates = lcm_fetch_array(lcm_query($sql));
print_r($author);
				echo '<form action = "save_key_dates.php">';
				echo '<input type="hidden" name = "id_client" value ='. $row['id_client'] .' />';
				echo '<p>Start: <input type="text"  id = "start" name = "start" class="auto-kal" data-kal="format: \'DD MMMM YYYY\'" value ="'. $dates['start'].'"/><input type = "submit" value = "clear" onclick = "javascript:document.getElementById(\'start\').value = \'\';"/></p>';
                                echo '<p>1st month review: <input type="text"  id="first" name = "first" class="auto-kal" data-kal="format: \'DD MMMM YYYY\'" value ="'. $dates['first'].'" /><input type = "submit" value = "clear" onclick = "javascript:document.getElementById(\'first\').value = \'\';"/></p>';
                                echo '<p>5th month review: <input type="text"  id="fifth" name = "fifth" class="auto-kal" data-kal="format: \'DD MMMM YYYY\'" value ="'. $dates['fifth'].'" /><input type = "submit" value = "clear" onclick = "javascript:document.getElementById(\'fifth\').value = \'\';"/></p>';
                                echo '<p>9th month review: <input type="text"  id = "ninth" name = "ninth" class="auto-kal" data-kal="format: \'DD MMMM YYYY\'" value ="'. $dates['ninth'].'" /><input type = "submit" value = "clear" onclick = "javascript:document.getElementById(\'ninth\').value = \'\';"/></p>';
                                echo '<p>Year end review: <input type="text"  id = "yearend" name = "yearend" class="auto-kal" data-kal="format: \'DD MMMM YYYY\'" value ="'. $dates['yearend'].'"  /><input type = "submit" value = "clear" onclick = "javascript:document.getElementById(\'yearend\').value = \'\';"/></p>';
                                echo '<p>Agreed end of Support: <input type="text" onclick="colourupdate(\'update\');" id = "supportend" name = "supportend" class="auto-kal" data-kal="format: \'DD MMMM YYYY\'" value ="'. $dates['supportend'].'"  /><input type = "submit" value = "clear" onclick = "javascript:document.getElementById(\'supportend\').value = \'\';"/></p>';
                                echo '<p>Agreed end of Accommodation: <input type="text" onclick="colourupdate(\'update\');" id = "accomend" name = "accomend" class="auto-kal" data-kal="format: \'DD MMMM YYYY\'" value ="'. $dates['accomend'].'"  /><input type = "submit" value = "clear" onclick = "javascript:document.getElementById(\'accomend\').value = \'\';"/></p>';
				echo '<input id = "update" type="submit" value="Update"></form>';

		}



echo "<br />";
				
// Clear session info
$_SESSION['client_data'] = array(); // DEPRECATED since 0.6.4
$_SESSION['form_data'] = array();
$_SESSION['errors'] = array();
matt_page_end();
lcm_page_end();
?>
