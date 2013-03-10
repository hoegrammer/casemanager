<?php

/*
	Holds combination of amount and bus pass
*/

class SupportCombo
{
	public $amount; // int
	public $bus_pass; // boolean

	/*
		Constructor takes either boolean or string
		for bus pass.

		@param int   $amount       required
		@param mixed $legal_reason required
		
	*/
	public function __construct($amount, $bus_pass)
	{
		if ($amount === null || $bus_pass === null) {
			throw new InvalidArgumentException(
				'No amount, or no legal reason / bus pass'	
			);
		}
		$this->amount = $amount;
		$this->bus_pass = $bus_pass === 'yes' || $bus_pass === '1';
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

	public function toString()
	{
		$str = '&pound;' . $this->amount;
		if ($this->bus_pass) {
			$str .= ' + BP';
		}
		return $str;
	}

}
