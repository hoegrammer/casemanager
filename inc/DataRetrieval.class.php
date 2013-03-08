<?php

/* Class containing static methods for retrieving data
from the DB. */

class DataRetrieval 
{

	public static function getAllCurrentlySupportedClientsNotUpdatedToday()
	{
		return self::_retrieve('select * from currently_supported
		where id_case not in (select id_case from lcm_followup
		where type = "followups27" and date(date_start) = date(now()))');
	}

	/*
		Runs a query and returns the data in an array

		@param string $sql the query
		
		@return array of data
	*/
	private static function _retrieve($sql) 
	{
		$result = lcm_query($sql);
		while ($row = mysql_fetch_assoc($result)) {
			$data[] = $row;
		}
		return $data;
	}

}
