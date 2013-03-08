<?php

/* Class containing static methods for retrieving data
from the DB. */

class DataModification 
{

	public static function saveWelfarePayment(WelfarePayment $welfare_payment)
	{
		global $author_session;
		$description = mysql_real_escape_string($welfare_payment->note);
		$id_author = $author_session['id_author'];
		$id_case   = mysql_real_escape_string($welfare_payment->id_case);
		$amount   = mysql_real_escape_string($welfare_payment->amount);
		$bus_pass = mysql_real_escape_string($welfare_payment->bus_pass);
		
		$sql = "insert into lcm_followup (type, id_author, description,
			date_start, id_case, outcome_amount, bus_pass_given)
			values ('followups27', $id_author, '$description',
			NOW(), $id_case, '$amount', '$bus_pass')";
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
