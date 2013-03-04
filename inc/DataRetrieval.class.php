<?php

/* Class containing static methods for retrieving data
from the DB. */

class DataRetrieval 
{
	/*
		name_first, name_last, id_client, amount, id_case
		for all open cases where amount > 0
	*/
	public static function getIdAmountAndClientNameForAllOpenCasesOverZero() 
	{
		$sql = 'select name_first, name_last, cl.id_client, amount, c.id_case 
	        from lcm_case as c 
        	left join lcm_case_client_org as cco on c.id_case = cco.id_case
	        left join lcm_client as cl on cl.id_client = cco.id_client
        	where c.amount > 0 and c.status="open"
	        order by cl.name_first, cl.name_last';

		return self::_retrieve($sql);	
	}

	/*
		Runs a query and returns the data in an array

		@param string $sql the query
		
		@return array of data
	*/
	private static function _retrieve($sql) 
	{
		$result = lcm_query($sql);
		while ($row = mysql_fetch_array($result)) {
			$data[] = $row;
		}
		return $data;
	}

}
