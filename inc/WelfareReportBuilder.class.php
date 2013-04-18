<?php

class WelfareReportBuilder
{
	private $_data; // raw report data

	public function __construct($data) {
		$this->_data = $data;
	}


	public function buildReport(Decorator $decorator)
	{
		$report = new WelfareReport();

		// Data is ordered by client. Add row and entries
		// for each client
		$id_client = null; // client currently being dealt with
		$reportRow = null; // WelfareReportRow currently being built
		foreach ($this->_data as $dataRow) {
			if ($dataRow['id_client'] !== $id_client) {
				$id_client = $dataRow['id_client'];
				// create a new row for this client
				$name_first = $dataRow['name_first'];
				$name_last = $dataRow['name_last'];
				$reportRow = new WelfareReportRow($name_first, $name_last, $id_client);
				$reportRow->decorate($decorator);
				$report->addRow($reportRow);
			}
			// add an entry for the date and payment
			$date_start = $dataRow['date_start'];
			$outcome_amount = $dataRow['outcome_amount'];
			$bus_pass_given = $dataRow['bus_pass_given'];
			$entry = new WelfareReportEntry($date_start, $outcome_amount, $bus_pass_given);
			$reportRow->addEntry($entry);
			// also ensure the report has the date, for column heading
			$report->addDate($date_start);
		}
		return $report;
	}
}

?>
