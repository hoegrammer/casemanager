<?php

/* Class containing static methods for retrieving data
from the DB. */

class DataModification 
{

	public static function saveAccompaniedBy($accompanied_by, $id_client)
        {
                $sql = "update lcm_client set regularly_accompanied_to_vulcan_house_by = '$accompanied_by' where id_client = $id_client";
                self::_execute($sql);
        }


        public static function deleteAllFaos()
        {
                $sql = "delete from lcm_faowelfaredesk";
                self::_execute($sql);
        }

	// always inserts. 
	public static function saveWelfarePayment(WelfarePayment $welfare_payment)
	{
		global $author_session;
		$description = mysql_real_escape_string($welfare_payment->note);
		$id_author = $author_session['id_author'];
		$id_case   = mysql_real_escape_string($welfare_payment->id_case);
		$amount   = mysql_real_escape_string($welfare_payment->amount);
		$bus_pass = mysql_real_escape_string($welfare_payment->bus_pass);
		$absent = $welfare_payment->absent;		
		
		$sql = "insert into lcm_followup (type, id_author, description,
			date_start, id_case, outcome_amount, bus_pass_given, absent)
			values ('followups27', $id_author, '$description',
			NOW(), $id_case, '$amount', '$bus_pass', '$absent')";
		self::_execute($sql);
	}


	// uses replace into.
	public static function saveFaoWelfareDesk(
		FaoWelfareDesk $faoWelfareDesk
	) {
		$id_client = $faoWelfareDesk->id_client;
		$amount = $faoWelfareDesk->amount;
		$bus_pass = (int)$faoWelfareDesk->bus_pass;
		$letter = (int)$faoWelfareDesk->letter;
		$advocacy = (int)$faoWelfareDesk->advocacy;
		$from_helpdesk = (int)$faoWelfareDesk->from_helpdesk;
		$note = $faoWelfareDesk->note;
		$sql = "replace into lcm_faowelfaredesk 
		(id_client, amount, bus_pass, letter, advocacy, from_helpdesk, note) values 
		($id_client, $amount, $bus_pass, $letter, $advocacy, $from_helpdesk, '$note')";
		self::_execute($sql);
	}

	/*
		Runs a query
		@param string $sql the query
	*/
	private static function _execute($sql) 
	{
		lcm_query($sql);
	}

}
