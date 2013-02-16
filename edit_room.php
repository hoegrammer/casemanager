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

//if ($author_session['status'] != 'admin')
//	{
//	echo 'Not allowed';
//	exit;
//	}

$id_room = intval(_request('room'), 0);

if (empty($_SESSION['errors'])) 
	{
	$form_data = array(/*d_room' => 0,*/'referer' => $_SERVER['HTTP_REFERER']);
	if ($id_room > 0) 
		{
		$q = 'SELECT * 
				FROM lcm_room
				WHERE id_room = ' . $id_room;

		$result = lcm_query($q);

		if ($row = lcm_fetch_array($result)) 
			{
			foreach($row as $key=>$value) 
				{
				$form_data[$key] = $value;
				}
			}
		}
	}	 
else 
	{
	// Fetch previously submitted values, if any
	if (! $_SESSION['form_data']['id_room'])
		$_SESSION['form_data']['id_room'] = 0;

	if (isset($_SESSION['form_data']))
		foreach($_SESSION['form_data'] as $key => $value)
			$form_data[$key] = $value;
	}

if ($id_room) 
	$title='Edit Room';
else 
	$title='Add New Room';

lcm_page_start($title);
matt_page_start($title.'...');
echo show_all_errors();

no_tabs();
echo '<form action="upd_room" method="post"/>';
echo '<table class="tbl_usr_dtl" width=100%>';

echo "<tr><td width='15%'>";
echo "Name:";
echo "</td><td>";
echo '<input name="name" value="' . clean_output($form_data['name']) . '" class="search_form_txt" />' . "\n";
echo "</td></tr>";

echo "<tr><td>";
echo "Type:";
echo "</td><td>";
echo '<select name="type">' . "\n";
echo '<option value="shared house" '.($form_data['type']=='shared house'?'selected':'').'>Shared house</option>';
echo '<option value="homestay" '.($form_data['type']=='homestay'?'selected':'').'>Homestay</option>';
echo '</select>';
echo "</td></tr>";

echo "<tr><td>";
echo "Host:";
echo "</td><td>";
$result1=lcm_query('select name_first, name_last, id_author from lcm_author where status="host"');
echo '<select name="host">' . "\n";
echo '<option value="0">No Host</option>';
while ($row1 = lcm_fetch_array($result1))
	{
	echo '<option value="'.$row1['id_author'].'" '.($form_data['host']==$row1['id_author']?'selected':'').'>';
	echo get_person_name($row1);
	echo '</option>';
	}
echo '</select>';
echo "</td></tr>";

echo "<tr><td>";
echo "Gender:";
echo "</td><td>";
echo '<select name="sex">' . "\n";
echo '<option value="both" '.($form_data['sex']=='both'?'selected':'').'>Both</option>';
echo '<option value="male" '.($form_data['sex']=='male'?'selected':'').'>Male</option>';
echo '<option value="female" '.($form_data['sex']=='female'?'selected':'').'>Female</option>';
echo '</select>';
echo "</td></tr>";

echo "<tr><td>";
echo "Other Notes:";
echo "</td><td>";
echo '<input name="note" value="' . clean_output($form_data['note']) . '" class="search_form_txt" />' . "\n";
echo "</td></tr>";

echo "<tr><td>";
echo "Available:";
echo "</td><td>";
echo '<input type="checkbox" name="available" '.($form_data['status']=='normal'?'checked':'').' value="yes" class="search_form_txt" />' . "\n";
echo "</td></tr>";

/*echo "<tr><td>";
echo "</td><td>";
echo "</td></tr>";*/

echo '</table>';
echo '<input type="hidden" name="id_room" value="'.$id_room.'"/>';

echo '<p><button name="submit" type="submit" value="submit" class="simple_form_btn">Submit</button></p>';
echo '</form>';










/*echo"<tr>";
echo"<td width=20%>";
echo"Start Date:";
echo "</td>";
echo "<td>";
$row=lcm_fetch_array(lcm_query('select p.* from lcm_placement as p where status="active" and id_room = '.$id_room));
if ($row['date_end']=='')
	{
	$the_date=date('Y-m-d');
	}
else
	{
	$the_date=$row['date_end'];
	}
echo get_date_inputs('date_start',$the_date,false);
echo"</td>";
echo"</tr>";	

echo '<input type="hidden" name="status" value="provisional" />';

echo '<tr><td>';
echo 'Select Client:<br />(From Waiting List)';
echo '</td><td>';
$q = "SELECT cl.* 
		from lcm_client as cl 
		left join lcm_case_client_org as cco on cl.id_client = cco.id_client 
		left join lcm_case as c on c.id_case = cco.id_case 
		where c.type_case = 'accomidation' and c.stage='waiting list'";
$result = lcm_query($q);
$checked = true;
while ($row = lcm_fetch_array($result))
	{
	echo "<input type='radio' ".($checked?'checked':'')." name='choose_client' value='".$row['id_client']."'>
			<a class='content_link' href='client_det.php?client=".$row['id_client']."'>".get_person_name($row)."</a>
			</radio>
			<br />";
	if ($checked) $checked=false;
	}
echo '</td></tr>';*/

	














/*	$when_day = _request('when_day');
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
	echo '<button name="submit" type="submit" value="submit" class="simple_form_btn">Refresh</button></p></form>';


	$q= 'SELECT *, r.id_room as foo from lcm_room as r left join 
			(
			select * from lcm_placement where
				(
				(to_days(date_start) < to_days(\''.$the_date.'\') AND to_days(date_end > to_days(\''.$the_date.'\')))
				OR
				(to_days(date_start) >= to_days(\''.$the_date.'\'))
				)
			) 
		as p on r.id_room = p.id_room where id_placement is null
		';
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
	//$headers[0]['order'] = 'order_short';
	//$headers[0]['default'] = 'ASC';
	$headers[1]['title'] = 'Type';
	//$headers[2]['title'] = 'Created Date';
	//$headers[2]['order'] = 'order_date';
	//$headers[2]['default'] = '';
	echo '<form action="update.php" method="get"> ';
	show_list_start($headers);
	for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) 
		{
		echo "<tr>\n";
		echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
		echo "<input type='radio' name='chosen_room' value='".$row['foo']."'></input><a href='room_det.php?id_room=".$row['foo']."'>" . $row['name'] . "</a>";
		echo "</td>\n";

		echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
		echo $row['type'];
		echo "</td>\n";

		echo "</tr>\n";
		}
	show_list_end($list_pos, $number_of_rows);
	

//	if ($GLOBALS['author_session']['status'] == 'admin')
//		echo '<p><a href="edit_room.php?crib=0" class="create_new_lnk">Add New Room</a></p>';
//	}





*/









matt_page_end();
lcm_page_end();

$_SESSION['errors'] = array();
$_SESSION['form_data'] = array();
?>
