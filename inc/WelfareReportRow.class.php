<?php

class WelfareReportRow
{
	// first and last name and client id will be
	// combined, when decorated, into public link
	private $_name_first;
	private $_name_last; 
	private $_id_client;

	// array of WelfareReportEntry objects. 
	// An entry is a welfare payment made on a date
	public $entries;

	public function __construct($name_first, $name_last, $id_client) {
		$this->_name_first = $name_first;
		$this->_name_last = $name_last;
		$this->_id_client = $id_client;
	}

	public function addEntry(WelfareReportEntry $entry)
	{
		$this->entries[] = $entry;
	}

	public function decorate(Decorator $decorator)
	{
		$this->link = $decorator->makeLink(
			$this->_id_client, $this->_name_first, $this->_name_last
		);
	}
}

?>
