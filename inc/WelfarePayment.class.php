<?php

/*
	A payment that has been made to a client.
	This is a value object so the fields are public.
*/

class WelfarePayment 
{
	public $id_case; 
	public $amount = 0; // in pounds
	public $bus_pass = 0; // 1 or 0
	public $note;
	public $absent; // 1 if they were absent

	public function __construct($id_case, $amount, $bus_pass, $note, $absent)
	{
		$this->id_case  = $id_case;
		$this->absent = $absent;
		if ($absent) {
			$this->amount   = 0;
			$this->bus_pass = 0;
			$this->note = "Did not attend.\n$note";
		} else {
			$this->amount   = $amount;
                        $this->bus_pass = $bus_pass;
			$this->note = $note;
		}
	}
}
