<?php
include('inc/inc.php');
include('inc/inc_reports.php');
include_lcm('inc_acc');

global $author_session;
global $prefs;


// Restrict page to administrators
if ($author_session['status'] != 'admin') {
	lcm_page_start(_T('title_site_configuration'), '', '', 'siteconfig');
	echo '<p class="normal_text">' . _T('warning_forbidden_not_admin') . "</p>\n";
	lcm_page_end();
	exit;
}

lcm_page_start('Set Report','','','');
matt_page_start('Set Report');
no_tabs();

$result=lcm_query("select id_client from lcm_client");


while ($row = lcm_fetch_array($result))
	{
	lcm_query("INSERT INTO lcm_case_");



	}






/*

echo '<form action="report2.php" method="post">';
//DATES LIST
echo '<table class="tbl_usr_dtl"><tr><td colspan=2>';
echo "<p>";
echo "Show records updated...<br />";
echo "<input type='radio' name='datetype' value='today'>today<br />";
echo "<input type='radio' name='datetype' value='lastmonth'>last month<br />";
echo "<input type='radio' name='datetype' value='thisyear'>this financial year<br />";
echo "<input type='radio' name='datetype' value='range' checked>on, or after: ";
echo get_date_inputs('date_start',$date_start);
echo " but before: ".get_date_inputs('date_end',$date_end);
echo '</p>';

//SHOW FUNDER DROP-DOWN
$q = "SELECT id_keyword, title FROM lcm_keyword WHERE id_group='24'";
$result = lcm_query($q);
echo '</td></tr>';
echo '<tr><td>';
echo '<select name="clfunder">';
echo '<option value="">n/a</option>';
while ($row = lcm_fetch_array($result))
	{
	echo '<option value="'.$row['id_keyword'].'">'.$row['title'].'</option>';
	}
echo '</select>';
echo '</td>';
echo '<td>';

echo '<i>Funder:</i> Selecting a funder will limit client based reports to clients attributed to that funder. Reports based on cases will be limited to cases which concern a client attributed to that funder. Reports based on Work items will be limited to those work items attached to cases concerning clients attributed to that funder';
echo '</td></tr>';

echo '<tr><td>';
echo '<select name="age">';
echo '<option value="new">Created</option>';
echo '<option value="old">Updated</option>';
echo '</select>';
echo '</td>';
echo '<td>';
echo '<i>Age: </i>This filter alters how the reports determine what records lie within a particular date range.<br/>For reports based on Clients, selecting "Created" will only show clients who where registered within the given date range. Selecting "Updated" will show clients who have seen some case activity within the given date range. This can be interpreted as "New clients, or new and returning clients".<br/>For reports based on Cases, selected "Created" will only show cases which were opened within the given date range. Selecting "Updated" will show cases which have seen some activity within the given date range. This can be interpreted as "New cases, or ongoing cases".<br/>For reports based on Work items, this filter has no effect, as work items are not considered to have a duration.';
echo '</td></tr></table><br/>';

show_page_subtitle ('Available Reports');

//SHOW REPORTS
echo '<ul class="info">';
foreach ($reports as $report)
	{
	echo "<li><input type='checkbox' name='".$report['name']."' value='".$report['name']."' />".$report['title']."</li>";
	}
echo '</ul>';
echo '<br/><button type="submit" class="simple_form_btn">Run...</button>';

echo '</form>';

*/
lcm_page_end();


?>
