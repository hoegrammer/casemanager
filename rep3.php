<?php

include('inc/inc.php');

global $author_session;
global $prefs;
lcm_page_start('Report','','','');
$rowtype=_request('rowtype');
$rowkey=_request('rowkey');
$coltype=_request('coltype');
$colkey=_request('colkey');
$select='1';
$from='';
$join='';
$where='1=1';
$groupby='';

if ($rowtype=='casekeyw')
	{
	$from='lcm_keyword as k';
	$join='RIGHT JOIN lcm_keyword_case as kc';
	$select='';
	}















echo '<table>';
if ($yes_case)
	{
	echo '<tr><td>Cases</td></tr>';
	echo '<tr>';
	echo '<form action="rep3.php" method="get">';
	echo '<input type="hidden" name="coltype" value="caseprop">';
	echo '<input type="hidden" name="rowtype" value="'.$rowtype.'">';
	echo '<input type="hidden" name="rowkey" value="'.$rowkey.'">';
	echo '<td>';
	echo '<select name="colkey">';
	echo '<option value="status">Status</option>';
	echo '</select>';
	echo '</td><td>';
	echo '<button name="submit" type="submit" value="submit" class="simple_form_btn">Submit</button>';
	echo '</td>';
	echo '</form>';
	echo '</tr>';
	
	echo '<tr>';
	$qtmp = "SELECT id_group, title, type FROM lcm_keyword_group WHERE type='case';";
	$result = lcm_query($qtmp);
	echo '<form action="rep3.php" method="get">';
	echo '<input type="hidden" name="coltype" value="casekeyw">';
	echo '<input type="hidden" name="rowtype" value="'.$rowtype.'">';
	echo '<input type="hidden" name="rowkey" value="'.$rowkey.'">';
	echo '<td>';
	echo '<select name="colkey">';
	while ($row = lcm_fetch_array($result))
		{
		echo "<option value='".$row['id_group']."'>";
		echo $row['title'];
		echo "</option>\n";
		}
	echo '</select>';
	echo '</td><td>';
	echo '<button name="submit" type="submit" value="submit" class="simple_form_btn">Submit</button>';
	echo '</td>';
	echo '</form>';
	echo '</tr>';
	}
if ($yes_client)
	{
	echo '<tr><td>Clients</td></tr>';
	echo '<tr>';
	echo '<form action="rep3.php" method="get">';
	echo '<input type="hidden" name="coltype" value="clientprop">';
	echo '<input type="hidden" name="rowtype" value="'.$rowtype.'">';
	echo '<input type="hidden" name="rowkey" value="'.$rowkey.'">';
	echo '<td>';
	echo '<select name="colkey">';
	echo '<option value="gender">Gender</option>';
	echo '</select>';
	echo '</td><td>';
	echo '<button name="submit" type="submit" value="submit" class="simple_form_btn">Submit</button>';
	echo '</td>';
	echo '</form>';
	echo '</tr>';
		
	echo '<tr>';
	$qtmp = "SELECT id_group, title, type FROM lcm_keyword_group WHERE type='client';";
	$result = lcm_query($qtmp);
	echo '<form action="rep3.php" method="get">';
	echo '<input type="hidden" name="coltype" value="clientkeyw">';
	echo '<input type="hidden" name="rowtype" value="'.$rowtype.'">';
	echo '<input type="hidden" name="rowkey" value="'.$rowkey.'">';
	echo '<td>';
	echo '<select name="colkey">';
	while ($row = lcm_fetch_array($result))
		{
		echo "<option value='".$row['id_group']."'>";
		echo $row['title'];
		echo "</option>\n";
		}
	echo '</select>';
	echo '</td><td>';
	echo '<button name="submit" type="submit" value="submit" class="simple_form_btn">Submit</button>';
	echo '</td>';
	echo '</form>';
	echo '</tr>';
	}
echo '</table>';

lcm_page_end;
?>
