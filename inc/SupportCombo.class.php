<?php

/*
	Holds combination of amount and bus pass
*/

class SupportCombo
{
	public $amount; // int
	public $bus_pass; // boolean

	/*
		Constructor takes array containing legal_reason
		field and translates it into bus pass boolean.

		@param array $data must contain amount and legal reason
	*/
	public function __construct(array $data)
	{
		if (!array_key_exists('amount', $data)
		    || !array_key_exists('legal_reason', $data)
		) {
			throw new InvalidArgumentException('No amount or no legal reason');
		}
		$this->amount = $data['amount'];
		$this->bus_pass = $data['legal_reason'] === 'yes';
	}
}
