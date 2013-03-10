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

		@param int    $amount       required
		@param string $legal_reason required
		
	*/
	public function __construct($amount, $legal_reason)
	{
		if ($amount === null || $legal_reason === null) {
			throw new InvalidArgumentException(
				'No amount, or no legal reason'	
			);
		}
		$this->amount = $amount;
		$this->bus_pass = $legal_reason === 'yes';
	}


        /*
                Create a default FAOWelfareDesk instance
                which contains the amount and bus pass from here
		plus defaults.

		@param int $id_client optional

                @return FAOWelfareDesk
        */
        public function createFAOWelfareDesk($id_client = null)
        {
                return new FAOWelfareDesk(
                        $id_client, $this->amount, 
                        $this->bus_pass, false, false, false, ''
                );
        }

}
