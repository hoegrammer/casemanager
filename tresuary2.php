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
if ($GLOBALS['author_session']['right7']==1)
	{
	$admin = true;
	}

$_SESSION['form_data']=array();
$_SESSION['errors']=array();
	
lcm_page_start('Welfare Payments');
matt_page_start('Welfare Payment Records');

$groups = array (
		'outstanding' => 'Post-its',
		'record' => 'Records',
	);
$tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'outstanding' );
show_tabs($groups,$tab,$_SERVER['REQUEST_URI']);

switch($tab)
	{
	case 'outstanding':
		include('notes.php');
		show_notes('post-tres','tresuary2.php',$admin);
		break;
	case 'record':
		if ($admin)
			{
			echo "<p><a class='edit_lnk' href='tresuary.php'>Record New Session</a></p>";
			}
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
				echo '<form action="tresuary2.php?tab=record" method="post"><p><small>Show payments made in :</small> ';
				echo get_date_inputs('when',$the_date,false,false,true);
				echo '<button name="submit" type="submit" value="submit" class="simple_form_btn">Refresh</button></p></form>';
				$min_date = $when_year.'-'.$when_month.'-'.'01 00:00:00';
				$max_date = $when_year.'-'.$when_month.'-'.'31 23:59:59';
				switch ($when_month)
					{
					case 1:case 3:case 5:case 7:case 8:case 10:case 12:$mdays=31;break;
					case 4:case 6:case 9:case 11:$mdays=30;break;
					default: 
						if ($when_year%400==0)
							$mdays=29;
						elseif ($when_year%100==0)
							$mdays=28;
						elseif ($when_year%4==0)
							$mdays=29;
						else
							$mdays=28;
					}

				$q='SELECT 
						concat(", SUM(IF(substr(fu.date_start,9,2) = \'",substr(date_start,9,2) ,"\',outcome_amount,0)) as D",substr(date_start,9,2),"" ) as zam 
					FROM 
						lcm_followup
					WHERE
						outcome_amount > 0 AND
						date_start >= \''.$min_date.'\' AND
						date_start <= \''.$max_date.'\'
					GROUP BY
						zam

					';
				$xtab = lcm_query($q);
				$q='SELECT
						concat("<a href=\'client_det.php?client=",cl.id_client,"\' class=\'content_link\'>",cl.name_first, cl.name_last,"</a>") as client
					';
				while ($x = lcm_fetch_array($xtab))
					{
					$q.=$x['zam'];
					}
				$q.='
					FROM
						lcm_client as cl
					LEFT JOIN
						lcm_case_client_org as cco on cco.id_client = cl.id_client
					LEFT JOIN
						lcm_followup as fu on fu.id_case = cco.id_case
					WHERE 
						outcome_amount > 0 AND
						fu.date_start >= \''.$min_date.'\' AND
						fu.date_start <= \''.$max_date.'\'
					GROUP BY
						client
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
		//		$headers[1]['title'] = 'X';
				for ($j=1;$j<=$mdays;$j++)
					{
					$k = ($j <10?'0'.$j:$j);
					$headers[$j]['title']="<small>".$k."</small>";
					}
				show_list_start($headers);
				for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) 
					{
					echo "<tr>\n";
					echo "<td width = '10%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
					echo $row['client'];
					echo "</td>\n";
					for ($j=1;$j<=$mdays;$j++)
						{
						$k = ($j <10?'0'.$j:$j);
						if ($row['D'.$k]>0)
							{
							echo "<td class='tbl_cont_".($i%2?"dark":"light")."'><small>".$row['D'.$k]."</small></td>";
							}
						else
							{
							echo "<td class='tbl_cont_".($i%2?"dark":"light")."'>-</td>";
							}
						}
					echo "</td>\n";
					echo "</tr>\n";
					}
				show_list_end($list_pos, $number_of_rows);
				break;
		/*	case 'day':
				echo '<form action="nightshelter.php" method="get"><p><small>Show payments made in:</small> ';
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
				break;*/
			}
		break;
	}
matt_page_end();
lcm_page_end();

?>
