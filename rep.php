<?php

include('inc/inc.php');

global $author_session;
global $prefs;
lcm_page_start('Report','','','');

echo '<table>';

echo '<tr><td>Cases</td></tr>';
echo '<tr>';
echo '<form action="rep2.php" method="get">';
echo '<input type="hidden" name="rowtype" value="caseprop">';
echo '<td>';
echo '<select name="rowkey">';
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
echo '<form action="rep2.php" method="get">';
echo '<input type="hidden" name="rowtype" value="casekeyw">';
echo '<td>';
echo '<select name="rowkey">';
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

echo '<tr><td>Clients</td></tr>';
echo '<tr>';
echo '<form action="rep2.php" method="get">';
echo '<input type="hidden" name="rowtype" value="clientprop">';
echo '<td>';
echo '<select name="rowkey">';
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
echo '<form action="rep2.php" method="get">';
echo '<input type="hidden" name="rowtype" value="clientkeyw">';
echo '<td>';
echo '<select name="rowkey">';
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
echo '</table>';		

lcm_page_end;
?>
