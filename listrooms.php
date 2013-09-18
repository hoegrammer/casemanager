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

global $author_session;

$admin = false;
if ($GLOBALS['author_session']['right5']==1)
	{
	$admin = true;
	}

$_SESSION['form_data']=array();
$_SESSION['errors']=array();
	
lcm_page_start();
matt_page_start('Rooms');
$groups = array(
	'all' => 'All',
	'available' => 'Available',
	'occupied' => 'Occupied',
	'unavailable' => 'Unavailable',
	);
$tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'all' );
show_tabs($groups,$tab,$_SERVER['REQUEST_URI']);

/*$when_day = _request('when_day');
$when_month = _request('when_month');
$when_year = _request('when_year');
if ($when_year < 1900 || $when_year > 9999) {$when_year = '';}
if ($when_month< 1 || $when_month > 12 ) {$when_month = '';}
if ($when_day < 1 || $when_day > 31 ) {$when_day = '';}
if (!$when_year) {$when_year= date('Y');}
if (!$when_month) {$when_month= date('m');}
if (!$when_day) {$when_day = date('d');}
$the_date = $when_year.'-'.$when_month.'-'.$when_day;
echo '<form action="listrooms.php" method="get"><p><small>Offer a room on:</small> ';
echo get_date_inputs('when',$the_date,false);
echo '<button name="submit" type="submit" value="submit" class="simple_form_btn">Refresh</button></p></form>';*/

$q= 'SELECT 
		r.*, 
		p.id_placement as pid, p.id_case as pca, p.date_start as pds, p.date_end as pde, p.status as pst,
		a.id_placement as aid, a.id_case as aca, a.date_start as ads, a.date_end as ade, a.status as ast,
		auth.name_first, auth.name_last, id_author
	from lcm_room as r 
	left join (select * from lcm_placement where status="active")as a on r.id_room = a.id_room
	left join (select * from lcm_placement where status="provisional") as p on r.id_room = p.id_room
	left join lcm_author as auth on auth.id_author = r.host
	where 1=1 ';
if ($tab=='available')
	{
	$q.='and a.id_placement is NULL ';
	$q.='and r.status = "normal" ';
	}
elseif ($tab=='occupied')
	{
	$q.='and a.id_placement';
	}
elseif ($tab=='unavailable')
	{
	$q.='and r.status = "unavailable"';
	}

$order_set = false;
$order_room = '';
$q .= " ORDER BY r.name";
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
$headers[0]['title'] = 'Room';
$headers[0]['order'] = 'order_room';
$headers[0]['default'] = 'ASC';
$headers[1]['title'] = 'Type';
$headers[1]['order'] = 'order_type';
$headers[2]['title'] = 'Details';
//$headers[3]['title'] = 'Actions';
//$headers[2]['title'] = 'Created Date';
//$headers[2]['order'] = 'order_date';
//$headers[2]['default'] = '';

show_list_start($headers);
for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) 
	{
//	if (    !(     ($tab=='available')&& (($row['aid'])||($row['status']!='normal'))    )    )
		{
		echo "<tr>\n";
		echo "<td width = '25%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
		echo "<a class='content_link' href='room_det.php?id_room=".$row['id_room']."'>" . $row['name'] . "</a><br /><br/> ";
		
		echo "</td><td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'><small>";
		
		echo $row['type'];
		if ($row['type']=='homestay')
			{
			if ($row['host'])
				{
				echo " with <br />";
				echo "<a class='content_link' href='author_det.php?author=".$row['id_author']."'>".get_person_name($row)."</a>";
				}
			else
				{
				echo ", no host listed";
				}
			}
		echo "</small></td>\n";

		echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
		if ($row['aid'] == '')
			{
			if ($row['status']=='normal')
				{
				echo "<b>Available</b>.";
				echo '<br />';
				}
			elseif ($row['status']=='unavailable')
				{
				echo "<b>Unavailable</b>.";
				echo '<br />';
				}
			}
		else
			{
			$client=lcm_fetch_array(lcm_query("
						SELECT cl.name_first, cl.name_last, cl.id_client, ap.*
						from lcm_case_client_org as cco natural 
						left join lcm_client as cl 
						LEFT JOIN ( select a.* from lcm_app as a where a.dismissed = false and a.title='accterm' ) as ap on ap.id_case = cco.id_case
						where cco.id_case='".$row['aca']."'
						"));
			echo "
				Occupied by <a href='client_det.php?client=".$client['id_client']."' class='content_link'><b>". get_person_name($client). '</b></a>
				, from: '.format_date($row['ads'], 'date_short').'.';
			if ($client['start_time'] != '')
				{
				echo ' <b>Due to leave on '.format_date($client['start_time'],'date_short').'.</b>';
				}
			echo '<br />';
			}
		if (!$row['pid'] == '')
			{
			$client=lcm_fetch_array(lcm_query("SELECT cl.name_first, cl.name_last, cl.id_client from lcm_case_client_org as cco natural left join lcm_client as cl where cco.id_case='".$row['pca']."'"));
			echo "Reserved for <a href='client_det.php?client=".$client['id_client']."' class='content_link'><b>". get_person_name($client). '</b></a>.';
			}
		if ($row['aid']=='' && $row['pid']=='')
			{
			echo '(Suitable for '.($row['sex']=='both'?'male or female':$row['sex']).' clients'.($row['note']?'.<br/>Extra Notes: '.$row['note']:'').')';
			}

		echo "</td>";
echo "<td><p class='normal_text'><a href='delete_room.php?id_room=".$row['id_room']."' class='add_lnk'>Delete</a></p></td>";

		echo "</td>";

		echo "</tr>\n";
		}
	}
show_list_end($list_pos, $number_of_rows);

if ($admin)
	echo '<p class="normal_text"><a href="edit_room.php" class="add_lnk">Add A New Room</a></p>';

matt_page_end();
lcm_page_end();

?>
