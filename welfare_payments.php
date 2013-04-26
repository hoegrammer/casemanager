<?php

// universally required (auth, etc) 
require 'inc/inc.php';

// required for getting information to display
require 'inc/DataRetrieval.class.php';

// header, sidebar etc.
lcm_page_start();
// heading for individual page
matt_page_start('Welfare Payments');

// Display title and tabs
$tab = htmlentities($_GET['tab']);
require 'inc/templates/welfare_payments_header.tpl';

// Decide what to do based on tab chosen.
switch ($tab)  {
	case "record":
		show_input_form();
		break;
	case "print":
		show_printable_sheet();
		break;
	case "report":
		show_report();
		break;
}	
	
function show_input_form() {
	// Get client names and ids for everyone who is currently financially supported
	// and does not have a welfare payment update for today
	$clients = DataRetrieval::getAllCurrentlySupportedClientsNotUpdatedToday();

	// if there are none, say so
	if (empty($clients)) {
		echo "All clients have already been updated.";
	} else {
		// otherwise display the form
		require 'inc/templates/welfare_payments_input.tpl';
	}
}

function show_report() {

	extract (setUpFilters());

	// Get welfare payment histories for the default period
	$data = DataRetrieval::getWelfarePaymentHistories($from_helpdesk, $support_type);

	// create a report from the data
	require 'inc/WelfareReportBuilder.class.php';
	require 'inc/WelfareReport.class.php';
	require 'inc/WelfareReportRow.class.php';
	require 'inc/WelfareReportEntry.class.php';
	require 'inc/Decorator.class.php';
	$builder = new WelfareReportBuilder($data);
	$decorator = new Decorator();
	$report = $builder->buildReport($decorator);
	
	// display the report
	global $tab;
	require 'inc/templates/welfare_payments_filters.tpl';
	require('inc/templates/welfare_payments_report.tpl');
}

function show_printable_sheet() {
	
	extract (setUpFilters());

	// get client name, usual support and FAO Welfare Desk information
	// for all supported clients
	$data = DataRetrieval::getWelfareSheetInformation($from_helpdesk, $support_type);
	
	// create rows, and a summary which combines them
	require 'inc/WelfareSheetRow.class.php';
	require 'inc/WelfareSheetSummary.class.php';
	require 'inc/SupportCombo.class.php';
	require 'inc/FAOWelfareDesk.class.php';
	require 'inc/Decorator.class.php';
	$decorator = new Decorator();
	$rows = WelfareSheetRow::createMany($data, $decorator);
	$summary = new WelfareSheetSummary($rows);
	$summary->calculate();
	global $tab;
	require 'inc/templates/welfare_payments_filters.tpl';
	require 'inc/templates/welfare_payments_sheet.tpl';
}

function setUpFilters()
{
	// from POST data, work out what to filter by and
	// ensure selections remain selected
        $selected = 'selected = "selected"';
        $from_helpdesk = null;
        // set up filters
        if ($_POST['collect_from'] === 'welfare') {
                // keep selected on GUI
                $welfare_selected = $selected;
                // to pass to DB
                $from_helpdesk = 0;
        } elseif ($_POST['collect_from'] === 'help') {
                $help_selected = $selected;
                $from_helpdesk = 1;
        }
        if (in_array( $_POST['support_type'], array('accommodated', 'not_accommodated'))) {
                $support_type = $_POST['support_type'];
                $support_type === 'accommodated' ? $accommodated_selected = $selected : $not_accommodated_selected = $selected;
        }

	return array(
		'from_helpdesk' => $from_helpdesk, 
		'welfare_selected' => $welfare_selected, 
		'help_selected' => $help_selected, 
		'support_type' => $support_type, 
		'accommodated_selected' => $accommodated_selected,
		'not_accommodated_selected' => $not_accommodated_selected
	);
}
