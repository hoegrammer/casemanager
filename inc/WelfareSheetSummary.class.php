<?php

class WelfareSheetSummary {
	
	private $_rows; // array of WelfareSheetRows
	
	public $total = 0; // integer, pounds
	public $amounts = array(); // array of counts of amounts
	public $busPasses = 0; // integer - amount of bus passes

	public function __construct(array $rows) {
		$this->_rows = $rows;
	}

	// from the rows, work out the amounts and total
	public function calculate() {
		foreach ($this->_rows as $row) {
			if (!isset($amounts[$row->thisWeekCash])) {
				$amounts[$row->thisWeekCash] = 0;
			} 	
			$this->amounts[$row->thisWeekCash] += 1;
			$this->total += $row->thisWeekCash;
			$this->busPasses += $row->thisWeekBusPass;
		}
		ksort($this->amounts);
	}
}
