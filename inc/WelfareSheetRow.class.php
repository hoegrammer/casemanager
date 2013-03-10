<?php

// Information to appear on printed welfare sheet.
// Contains usual support information plus anything
// entered in the FAO Welfare Desk form.

class WelfareSheetRow
{
	public $usualSupportCombo; 
	public $faoWelfareDesk; 

	/*
		construct from array containing
		usual support information as well
		as FAOWelfareDesk information
	*/
	__construct(array $data) {
		$this->usualSupportCombo = new SupportCombo(
			$data['usual_amount'], $data['legal_reason']
		);
	}
}
