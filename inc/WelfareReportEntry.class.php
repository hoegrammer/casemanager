<?php

// a single cell in the report matrix

class WelfareReportEntry
{
	public $date;
	public $amount;
	public $bus_pass;
	

	public function __construct($date, $amount, $bus_pass)
	{
		$this->date = $date;
		$this->amount = $amount;
		$this->bus_pass = $bus_pass;
	}

	public function decorate(Decorator $decorator)
	{
		return $decorator->makeAmountAndBusPassCells($this->amount, $this->bus_pass);
	}

}

?>
