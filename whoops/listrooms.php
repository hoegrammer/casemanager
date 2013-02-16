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

//if ($author_session['status'] != 'admin')
//	{
//	echo 'Not allowed';
//	exit;
//	}

if (_request('case') =='')
	{
	echo 'Error! No Case';
	exit;
	}

if (! isset($_SESSION['form_data']))
	{
	$_SESSION['form_data']= array();
	}

$_SESSION['form_data']['ref_listrooms'] = _request('ref');

print_r($_SESSION['form_data']);
	
lcm_page_start();
matt_page_start('Offer a Room...');

echo show_all_errors($_SESSION['errors']);

$fu = new LcmFollowupInfoUI();
$fu->data['type']='room_offer';
$fu->printEdit();

$when_day = _request('when_day');
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
echo '<form action="upd_placement.php" method="get"> ';
show_list_start($headers);
for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) 
	{
	echo "<tr>\n";
	echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo "<input type='radio' name='room' value='".$row['foo']."'></input><a href='room_det.php?id_room=".$row['foo']."'>" . $row['name'] . "</a>";
	echo "</td>\n";

	echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo $row['type'];
	echo "</td>\n";

	echo "</tr>\n";
	}
show_list_end($list_pos, $number_of_rows);
echo '<input type="hidden" name="type" value="make_prov" />';
echo '<input type="hidden" name="case" value="'._request($case).'" />';
echo '<input type="hidden" name="when_year" value="'.$when_year.'" />';
echo '<input type="hidden" name="when_day" value="'.$when_day.'" />';
echo '<input type="hidden" name="when_month" value="'.$when_month.'" />';
echo '<button name="submit" type="submit" value="submit" class="simple_form_btn">Submit</button>';
echo '</form>';
	

matt_page_end();
lcm_page_end();

?>
