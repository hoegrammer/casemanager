<?php

// Information to appear on printed welfare sheet.
// Contains usual support information plus anything
// entered in the FAO Welfare Desk form. Also client name.

class WelfareSheetRow
{
	public $usualSupportCombo; 
	public $faoWelfareDesk; 
	public $client_name;

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
		$this->usualSupportCombo = new SupportCombo(
			$usual_amount, $legal_reason
		);

		// if there is nothing in $data['fao_amount'] (i.e. not
		// even 0) then there has been no FAO created for this client
		// and we need to create a default one.
		if ($fao_amount === null) {
			$this->faoWelfareDesk 
				= $this->usualSupportCombo->createFAOWelfareDesk($id_client);
		} else {
			$this->faoWelfareDesk = new FAOWelfareDesk (
				$id_client, $fao_amount, $bus_pass, $letter, 
				$advocacy, $from_helpdesk, $note
			);
		}	
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
