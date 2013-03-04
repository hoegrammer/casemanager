<?php

include('inc/inc.php');
include_lcm('inc_acc');
include_lcm('inc_filters');
include_lcm('inc_obj_fu');
include('inc/DataRetrieval.class.php');


global $author_session;

$_SESSION['form_data']=array();
$_SESSION['errors']=array();
	
lcm_page_start('Welfare Payments');
matt_page_start('Welfare Payments');

echo '<form name="form" action="tresave.php" method="post" onSubmit="var but = document.forms[\'form\'][\'submit\']; but.disabled=true; but.innerHTML=\'Please wait\'; return true;">';

$fu_zomg = new lcmFollowupInfoUI();
$fu_zomg->data['type']='opening';
$fu_zomg->printEdit();

// Retrieve all the data that is to be displayed
$data = DataRetrieval::getDataForWelfarePaymentsEntrySheet();

$headers = array();
$headers[0]['title'] = 'Client';
$headers[1]['title'] = 'Normal';
$headers[2]['title'] = 'This Week';
$headers[3]['title'] = 'Attended?';
$headers[4]['title'] = 'Notes';
$headers[5]['title'] = 'Signature';

show_list_start($headers);
$i = 0;
foreach ($data as $row)
{
	extract($row);
	echo "<tr>\n";
	echo "<td  width = '20%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo "<a class='content_link' href='client_det.php?client=$id_client'>";
	echo "$client_name_first $client_name_last</a>";
	echo "</td>\n";

	// Normal support amount and whether they have a bus pass	
	echo "<td width = '5%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo "&pound;$amount";
	if ($bus_pass) {
		echo " + Bus Pass";
	}
	echo "</td>\n";	

	// Support amount for this week (select) and bus pass (select)
	echo "<td width = '10%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo "<select name='amount_$id_case'>";
	for ($j=0;$j<=60;$j=$j+5)
	{
		$sel='';
		if (clean_output($amount==$j))
			$sel='selected';
		echo '<option value="'.$j.'" '.$sel.'>'.$j.'</option>';
	}
	echo '</select>';
	echo "<select name='bus_pass_$id_case'>";
	echo "<option value=0 ";
	echo $bus_pass ? '' : 'selected=selected';
	echo "></option>";
	echo "<option value=1 ";
	echo $bus_pass ? 'selected=selected' : '';
	echo "> + Bus Pass</option>";
        echo '</select>';
        echo "</td>\n";


	echo "<td width = '10%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo "<input type='checkbox' name='check_$id_case'/>";
	echo "</td>\n";

	echo "<td width = '55%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	
	// if there is a note, then display it and its metadata and box to dismiss
	if ($row['note']) {
		echo "$note <small><i>(by $author_name_first $author_name_last on "; 
		echo format_date($date_creation,'date_short').')</i>';
		echo " &nbsp; Delete<input type='checkbox' name='dismiss_$id_app'/></small><br />";
	}
	echo "<input type='text' name='note_".$row['id_case']."' class='search_form_txt'/>";
	echo "</td>\n";

	echo "<td width = '10%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo "<br><br>...................................";
	echo "</td>\n";
	
	echo "</tr>\n";
	$i++;
}
echo "</table>";
echo "<p><button name='submit' value='submit' class='simple_form_btn'>Submit</button></p>";
echo "</form>";


matt_page_end();
lcm_page_end();

?>
