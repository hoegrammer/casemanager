<?php

/*
	Information for welfare desk staff about a particular client.
*/

class FAOWelfareDesk
{
	public $id_client; // int
	public $amount; // int
	public $bus_pass; //bool
	public $letter;// bool - letter awaiting client
	public $advocacy; // bool - client has appointment
	public $from_helpdesk; // bool - collect from helpdesk
	public $note; // string

	public function __construct(
		$id_client, $amount, $bus_pass, $letter, $advocacy, $from_helpdesk, $note
	) {
		$this->id_client = $id_client;
		$this->amount = $amount;
		$this->bus_pass = $bus_pass;
		$this->letter = $letter;
		$this->advocacy = $advocacy;
		$this->from_helpdesk = $from_helpdesk;
		$this->note = $note;
	}

	/*
		Create a default one using a client's
		usual support combo.

		@param SupportCombo $supportCombo

		@return FAOWelfareDesk
	*/
	public static function createFromSupportCombo(
		$id_client, SupportCombo $supportCombo
	) {
		return new FAOWelfareDesk(
			$id_client, $supportCombo->amount, 
			$supportCombo->bus_pass, false, false, false, ''
		);
	}
}
