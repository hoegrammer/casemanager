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
}

?>
