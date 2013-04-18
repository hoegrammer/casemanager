<?php

// a single cell in the report matrix

class WelfareReportEntry
{
	public $date;
	private $_amount;
	private $_bus_pass;
	

	public function __construct($date, $amount, $bus_pass)
	{
		$this->date = $date;
		$this->_amount = $amount;
		$this->_bus_pass = $bus_pass;
	}

	public function decorate(Decorator $decorator)
	{
		return $decorator->makeAmountAndBusPassCells($this->_amount, $this->_bus_pass);
	}

}

?>
