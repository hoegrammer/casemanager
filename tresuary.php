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

$_SESSION['form_data']=array();
$_SESSION['errors']=array();
	
lcm_page_start('Welfare Payments');
matt_page_start('Welfare Payments');
//if ($author_session['status'] != 'admin')
//	{
//	no_tabs();
//	echo "You do not have access to this area.";
//	matt_page_end();
//	lcm_page_end();
//	exit;
//	}



echo '<form name="form" action="tresave.php" method="post" onSubmit="var but = document.forms[\'form\'][\'submit\']; but.disabled=true; but.innerHTML=\'Please wait\'; return true;">';

$fu_zomg = new lcmFollowupInfoUI();
$fu_zomg->data['type']='opening';
$fu_zomg->printEdit();

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

$q= 'select c.*, cl.*
	from lcm_case as c 
	left join lcm_case_client_org as cco on c.id_case = cco.id_case
	left join lcm_client as cl on cl.id_client = cco.id_client
	where c.amount > 0 and c.status="open"
	order by cl.name_first, cl.name_last
	';
$result = lcm_query($q);
$number_of_rows = lcm_num_rows($result);


$headers = array();
$headers[0]['title'] = 'Client';
$headers[1]['title'] = 'Normal';
$headers[2]['title'] = 'This Week';
$headers[3]['title'] = 'Attended?';
$headers[4]['title'] = 'Notes (tick box to dismiss)';
$headers[5]['title'] = 'Signature';

show_list_start($headers);
$i = 0;
while ($row = lcm_fetch_array($result))
	{
	echo "<tr>\n";
	echo "<td  width = '20%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo "<a class='content_link' href='client_det.php?client=".$row['id_client']."'>" . get_person_name($row) . "</a>";
	echo "</td>\n";
	
	echo "<td width = '5%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo "£".$row['amount'];
	echo "</td>\n";	
	echo "<td width = '10%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo '<select name="amount_'.$row['id_case'].'">';
	for ($j=0;$j<=60;$j=$j+5)
		{
		$sel='';
		if (clean_output($row['amount'])==$j)
			$sel='selected';
		echo '<option value="'.$j.'" '.$sel.'>'.$j.'</option>';
		}
	echo '</select>';
	echo "</td>\n";

	echo "<td width = '10%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo "<input type='checkbox' name='check_".$row['id_case']."'/>";
	echo "</td>\n";

	echo "<td width = '55%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	$q = "
		SELECT ap.*, a.*
		FROM lcm_app as ap
		LEFT JOIN lcm_case_client_org as cco on cco.id_case = ap.id_case
		LEFT JOIN lcm_client as cl on cl.id_client = cco.id_client	
		LEFT JOIN lcm_author as a on a.id_author = ap.id_author
		WHERE ap.dismissed = 0 AND ap.title='tres' AND cl.id_client=".$row['id_client']."
		";
	$notes = lcm_query($q);
	while ($note = lcm_fetch_array($notes))
		{
		echo "<input type='checkbox' name='dismiss_" . $note['id_app']."'/>";
		echo $note['description'].' <small><i>(by '.get_person_name($note).' on '.format_date($note['date_creation'],'date_short').')</i></small><br />';
		}
	echo "<input type='text' name='note_".$row['id_case']."' class='search_form_txt'/>";
	echo "</td>\n";

	echo "<td width = '10%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo "<br><br>...................................";
	echo "</td>\n";
	
	echo "</tr>\n";
	$i++;
	}
//show_list_end($list_pos, $number_of_rows);
echo "</table>";
echo "<p><button name='submit' value='submit' class='simple_form_btn'>Submit</button></p>";
echo "</form>";

show_page_subtitle('Cash Totals');
$result = lcm_query('select amount, count(id_case) as ttl from lcm_case where status="open" and amount > 0 group by amount');

echo "<table class='tbl_usr_dtl'><tr><td><b>Quantity</b></td><td><b>Amount</b></td></tr>";
while ($row = lcm_fetch_array($result))
	{
	echo "<tr><td>";
	echo $row['ttl'];
	echo "</td><td>";
	echo $row['amount'];
	echo "</td></tr>";
	}

$row = lcm_fetch_array(lcm_query('select sum(amount) ttl from lcm_case where amount > 0'));
echo "<tr><td><b>Total:</b></td><td>";
echo $row['ttl'];
echo "</td></tr>";


echo "</table>";

matt_page_end();
lcm_page_end();

?>
