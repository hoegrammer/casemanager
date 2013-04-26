<?php

class WelfareReport
{
	public $rows; // array of WelfareReportRow objects
	public $dates; // for column headings. String array

	public function addRow(WelfareReportRow $row)
	{
		$this->rows[] = $row;
	}

	public function addDate($date)
        {
		// don't add it if it is already there
		if (!in_array($date, $this->dates)) {
	                $this->dates[] = $date;
		}
        }

	public function createDummyEntries()
	{
		// for any combination of client and date
		// that has no entry, create a dummy one.
		foreach($this->rows as $row) {
			foreach($this->dates as $date) {
				$row->addDummyEntryIfNeeded($date);	
			}
		}
		
	}
	
	public function sortDates()
	{
		rsort($this->dates);
	}
}

?>
