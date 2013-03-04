<?php

/* Class containing static methods for retrieving data
from the DB. */

class DataRetrieval 
{
	/*
		Gets client and case data for any case with an amount over 0.
		Also gets any welfare payment notes they may have
	*/
	public static function getDataForWelfarePaymentsEntrySheet() 
	{
		$sql = 'select cl.name_first as client_name_first, cl.name_last as client_name_last, 
		cl.id_client, a.name_first as author_name_first, a.name_last as author_name_last,
		description as note, amount, c.id_case, ap.id_app, ap.date_creation
	        from 
			lcm_case as c 
		left join 
			(select id_app, id_case, id_author, description, date_creation from lcm_app
			 where dismissed = 0 AND title="tres") ap
			on c.id_case = ap.id_case
        	join 
			lcm_case_client_org as cco on c.id_case = cco.id_case
	        join 
			lcm_client as cl on cl.id_client = cco.id_client
		left join
			lcm_author as a on a.id_author = ap.id_author
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
