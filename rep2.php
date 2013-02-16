<?php

include('inc/inc.php');

global $author_session;
global $prefs;
lcm_page_start('Report','','','');
$rowtype=_request('rowtype');
$rowkey=_request('rowkey');
$yes_client=0;
$yes_case=0;
$yes_followup=0;
$yes_author=0;
print $rowkey;
if (($rowtype=='caseprop')||($rowtype=='casekeyw'))
	{
	$yes_case=1;
	}
elseif (($rowtype=='clientprop')||($rowtype=='clientkeyw'))
	{
	$yes_client=1;
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
