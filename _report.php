<?php

include('inc/inc.php');

global $author_session;
global $prefs;
lcm_page_start('Report','','','');

$stage=_request('stage');
if (!$stage)
	{
	$stage=1;
	}

if ($stage==1)
	{
	echo '<form action="report.php" method="get">';
	echo '<input type="hidden" name="stage" value="2">';
	echo '<p>Show infomation about:</p>';
	echo '<select name="rows">';
	echo '<option value="case">Cases</option>';
	echo '<option value="client">Clients</option>';
	echo '</select>';
	echo '<button type="submit" class="simple_form_btn">Next...</button>';
	echo '</form>';
	}
elseif ($stage==2)
	{
	$rows=_request('rows');	
	if ($rows=='case')
		{
		echo '<form action="report.php" method="get">';
		echo '<input type="hidden" name="stage" value="3">';
		echo '<input type="hidden" name="rows" value="'.$rows.'">';
		echo '<select name="key">';
		echo '<option value="status">Status</option>';
		echo '</select>';
		echo '<button name="style" type="submit" value="prop" class="simple_form_btn">Next...</button>';
		echo '</form>';

		$qtmp = "SELECT id_group, title, type FROM lcm_keyword_group WHERE type='case';";
		$result = lcm_query($qtmp);
		echo '<form action="report.php" method="get">';
		echo '<input type="hidden" name="stage" value="3">';
		echo '<input type="hidden" name="rows" value="'.$rows.'">';
		echo '<select name="key">';
		while ($row = lcm_fetch_array($result))
			{
			echo "<option value='".$row['id_group']."'>";
			echo $row['title'];
			echo "</option>\n";
			}
		echo '</select>';
		echo '<button name="style" type="submit" value="keyw" class="simple_form_btn">Next...</button>';
		echo '</form>';
		}
	elseif ($rows=='client')
		{

		}
	}
elseif ($stage==3)
	{
	$rows=_request('rows');
	$style=_request('style');
	$key=_request('key');
	$yes_case=0;
	$yes_client=0;
	$yes_author=0;
	$yes_followup=0;
	if ($rows=='case')
		{
		$yes_case=1;		
		}
	elseif ($rows=='client')
		{
		$yes_client=1;
		}
	echo '<form action="report.php" method="get">';
	echo '<input type="hidden" name="stage" value="4">';
	echo '<input type="hidden" name="rows" value="'.$rows.'">';
	echo '<input type="hidden" name="style" value="'.$style.'">';
	echo '<input type="hidden" name="key" value="'.$key.'">';
	echo '<select name="cols">';
	echo if '<option value="status">Status</option>';
	echo '</select>';
	echo '<button name="style" type="submit" value="prop" class="simple_form_btn">Next...</button>';
	echo '</form>';
	}
else
	{
	
	}













/*
echo '<table>';

echo '<tr><td>Cases</td></tr>';
echo '<tr>';
echo '<form action="rep2.php" method="get">';
echo '<input type="hidden" name="choose" value="case1">';
echo '<td>';
echo '<select name="zor">';
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
echo '<input type="hidden" name="choose" value="case2">';
echo '<td>';
echo '<select name="zor">';
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
echo '<input type="hidden" name="choose" value="client1">';
echo '<td>';
echo '<select name="zor">';
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
echo '<input type="hidden" name="choose" value="client2">';
echo '<td>';
echo '<select name="zor">';
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

// CASES
//q = "SELECT id_group, title, type FROM lcm_keyword_group WHERE type='case';";
//$result = lcm_query($q);
//echo "<form action='rep2.php' method='get'>\n";
//echo "<p>Case</p>";
//echo "<select name='matt_group'>\n";
//while ($row = lcm_fetch_array($result))
//	{
//	echo "<option value='".$row['id_group']."'>";
//	echo $row['title'];
//	echo "</option>\n";
//	}
//echo "</select>\n";
//echo "<input type='hidden' name='matt_type' value='case'/>";
//echo "<button name='submit' type='submit' value='submit' class='simple_form_btn'>Submit</button>\n";
//echo "</form>\n";

//CLIENTS
//$q = "SELECT id_group, title, type FROM lcm_keyword_group WHERE type='client';";
//$result = lcm_query($q);
//echo "<form action='rep2.php' method='get'>\n";
//echo "<p>Client</p>";
//echo "<select name='matt_group'>\n";
//while ($row = lcm_fetch_array($result))
//	{
//	echo "<option value='".$row['id_group']."'>";
//	echo $row['title'];
//	echo "</option>\n";
//	}
//echo "</select>\n";
//echo "<input type='hidden' name='matt_type' value='client'/>";
//echo "<button name='submit' type='submit' value='submit' class='simple_form_btn'>Submit</button>\n";
//echo "</form>\n";


//FOLLOWUPS
//$q = "SELECT id_group, title, type FROM lcm_keyword_group WHERE type='followup';";
//$result = lcm_query($q);
//echo "<form action='rep2.php' method='get'>\n";
//echo "<p>Followup</p>";
//echo "<select name='matt_group'>\n";
//echo "<option value='12'>Outcome</optoin>";
//while ($row = lcm_fetch_array($result))
//	{
//	echo "<option value='".$row['id_group']."'>";
//	echo $row['title'];
//	echo "</option>\n";
//	}
//echo "</select>\n";
//echo "<input type='hidden' name='matt_type' value='followup'/>";
//echo "<button name='submit' type='submit' value='submit' class='simple_form_btn'>Submit</button>\n";
//echo "</form>\n";
*/
lcm_page_end;
?>
