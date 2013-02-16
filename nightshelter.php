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
if ($GLOBALS['author_session']['right8']==1)
	{
	$admin = true;
	}

$_SESSION['form_data']=array();
$_SESSION['errors']=array();
	


lcm_page_start('Night Shelter');
matt_page_start('Night Shelter Records');
//if ($author_session['status'] != 'admin')
//	{
//	no_tabs();
//	echo "You do not have access to this area.";
//	matt_page_end();
//	lcm_page_end();
//	exit;
//	}
//$groups = array(
//	'range' => 'Month',
//	'day' => 'Day',
//	);
//$tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'range' );
//show_tabs($groups,$tab,$_SERVER['REQUEST_URI']);
$groups = array (
		'outstanding' => 'Post IT\'s',
		'record' => 'Records',
	);
$tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'outstanding' );
show_tabs($groups,$tab,$_SERVER['REQUEST_URI']);

switch($tab)
	{
	case 'outstanding':
		include('notes.php');
		show_notes('post-ns','nightshelter.php',$admin);
		break;
	case 'record':
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
		$tab='range';

		switch ($tab) {
			case 'range':
				echo '<form action="nightshelter.php?tab=record" method="post"><p><small>Show shelter useage in:</small> ';
				echo get_date_inputs('when',$the_date,false,false,true);
				echo '<button name="submit" type="submit" value="submit" class="simple_form_btn">Refresh</button></p></form>';
				$min_date = $when_year.'-'.$when_month.'-'.'01 00:00:00';
				$max_date = $when_year.'-'.$when_month.'-'.'31 23:59:59';
				$q='SELECT
						to_days(fu.date_start) as days,
						fu.date_start as date,
						group_concat(" <a class=\'content_link\' href=\'client_det.php?client=",cl.id_client,"\'>",cl.name_first," ",cl.name_last,"</a>") as clients
					FROM
						lcm_followup as fu
					LEFT JOIN
						lcm_case_client_org as cco on cco.id_case = fu.id_case
					LEFT JOIN
						lcm_client as cl on cco.id_client = cl.id_client
					WHERE
						fu.type = \'followups30\' AND
						fu.date_start >= \''.$min_date.'\' AND
						fu.date_start <= \''.$max_date.'\'
					GROUP BY
						days
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
				$headers[0]['title'] = 'Date';
				$headers[1]['title'] = 'Client(s)';

				show_list_start($headers);
				for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) 
					{
					echo "<tr>\n";
					echo "<td width = '10%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
				//	echo "<a class='content_link' href='nightshelter.php?tab=day&when_day=".substr($row['date'],8,2)."&when_month=".$when_month."&when_year=".$when_year."'>" . substr($row['date'],8,2) . "</a>";
					echo substr($row['date'],8,2); 
					echo "</td>\n";
					echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
					echo $row['clients'];
					echo "</td>\n";
					echo "</tr>\n";
					}
				show_list_end($list_pos, $number_of_rows);
				break;
			case 'day':
				echo '<form action="nightshelter.php" method="get"><p><small>Show shelter users on:</small> ';
				echo get_date_inputs('when',$the_date,false);
				echo '<input type="hidden" name="tab" value="day">';
				echo '<button name="submit" type="submit" value="submit" class="simple_form_btn">Refresh</button></p></form>';

				$q='SELECT 
						cl.*
					FROM 
						lcm_followup as fu
					LEFT JOIN
						lcm_case_client_org as cco on fu.id_case = cco.id_case
					LEFT JOIN
						lcm_client as cl on cco.id_client = cl.id_client
					WHERE
						fu.type = \'followups30\' and
						to_days(fu.date_start) = to_days(\''.$the_date.'\')
					GROUP BY id_client;
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
				$headers[0]['title'] = 'Client';
				//$headers[0]['order'] = 'order_short';
				//$headers[0]['default'] = 'ASC';
				//$headers[2]['title'] = 'Created Date';
				//$headers[2]['order'] = 'order_date';
				//$headers[2]['default'] = '';

				show_list_start($headers);
				for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) 
					{
					echo "<tr>\n";
					echo "<td width = '20%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
					echo "<a class='content_link' href='client_det.php?client=".$row['id_client']."'>" . get_person_name($row) . "</a>";
					echo "</td>\n";
					echo "</tr>\n";
					}
				show_list_end($list_pos, $number_of_rows);
				break;
			}
		break;
	}
		

matt_page_end();
lcm_page_end();

?>
