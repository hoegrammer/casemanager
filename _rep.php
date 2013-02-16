<?php

include('inc/inc.php');

global $author_session;
global $prefs;

lcm_page_start('Report','','','');

// CASES
$q = "SELECT id_group, title, type FROM lcm_keyword_group WHERE type='case';";
$result = lcm_query($q);
echo "<form action='rep2.php' method='get'>\n";
echo "<p>Case</p>";
echo "<select name='matt_group'>\n";
while ($row = lcm_fetch_array($result))
	{
	echo "<option value='".$row['id_group']."'>";
	echo $row['title'];
	echo "</option>\n";
	}
echo "</select>\n";
echo "<input type='hidden' name='matt_type' value='case'/>";
echo "<button name='submit' type='submit' value='submit' class='simple_form_btn'>Submit</button>\n";
echo "</form>\n";

//CLIENTS
$q = "SELECT id_group, title, type FROM lcm_keyword_group WHERE type='client';";
$result = lcm_query($q);
echo "<form action='rep2.php' method='get'>\n";
echo "<p>Client</p>";
echo "<select name='matt_group'>\n";
while ($row = lcm_fetch_array($result))
	{
	echo "<option value='".$row['id_group']."'>";
	echo $row['title'];
	echo "</option>\n";
	}
echo "</select>\n";
echo "<input type='hidden' name='matt_type' value='client'/>";
echo "<button name='submit' type='submit' value='submit' class='simple_form_btn'>Submit</button>\n";
echo "</form>\n";


//FOLLOWUPS
$q = "SELECT id_group, title, type FROM lcm_keyword_group WHERE type='followup';";
$result = lcm_query($q);
echo "<form action='rep2.php' method='get'>\n";
echo "<p>Followup</p>";
echo "<select name='matt_group'>\n";
echo "<option value='12'>Outcome</optoin>";
while ($row = lcm_fetch_array($result))
	{
	echo "<option value='".$row['id_group']."'>";
	echo $row['title'];
	echo "</option>\n";
	}
echo "</select>\n";
echo "<input type='hidden' name='matt_type' value='followup'/>";
echo "<button name='submit' type='submit' value='submit' class='simple_form_btn'>Submit</button>\n";
echo "</form>\n";

lcm_page_end;
?>
