<?php

// Information to appear on printed welfare sheet.
// Contains usual support information plus anything
// entered in the FAO Welfare Desk form. Also client name.

class WelfareSheetRow
{
	public $usualSupport; //string 
	public $thisWeekSupport; //string
	public $client_name; // first and last
	public $client_id;
	public $letter; // bool
	public $advocacy; // bool
	public $collect_from; // string

	/*
		construct from array containing
		usual support information as well
		as FAOWelfareDesk information
	*/

	public function __construct(
		$id_client, $name_first, $name_last, $usual_amount, $legal_reason, $fao_amount, $bus_pass, 
		$letter, $advocacy, $from_helpdesk, $note
	) {
		$this->client_name = $name_first . ' ' . $name_last;
		$usualSupport_obj = new SupportCombo($usual_amount, $legal_reason);
		$this->usualSupport = $usualSupport_obj->toString();

		// If there is no FAO regarding support, set this week same as normal
		if ($fao_amount === null) {
			$this->thisWeekSupport = $this->usualSupport;
		} else {
			$thisWeekSupport_obj = new SupportCombo($fao_amount, $bus_pass);
			$this->thisWeekSupport = $thisWeekSupport_obj->toString();
		}	
		$this->letter = $letter;
		$this->advocacy = $advocacy;
		$this->collect_from = $from_helpdesk ? 'Helpdesk' : 'Welfare' ;
	}

	public static function createMany(array $data) 
	{
		foreach ($data as $row) {
			$rows[] = new WelfareSheetRow(
				$row['id_client'], $row['name_first'], $row['name_last'],
				$row['usual_amount'], $row['legal_reason'], $row['fao_amount'],
				$row['bus_pass'], $row['letter'], 
                                $row['advocacy'], $row['from_helpdesk'], $row['note']
			);
		}
		return $rows;
	}
}
