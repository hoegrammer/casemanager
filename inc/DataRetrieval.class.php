<?php

/* Class containing static methods for retrieving data
from the DB. */

class DataRetrieval 
{

	/*
		Gets data about clients who are currently (financially)
		supported and have not had a welfare desk update today.
	*/
	public static function getAllCurrentlySupportedClientsNotUpdatedToday()
	{
		return self::_retrieve('select * from currently_supported
		where id_case not in (select id_case from lcm_followup
		where type = "followups27" and date(date_start) = date(now()))');
	}

	/*
		Says whether a client is currently (financially)
		supported

		@param int $id_client required
		
		@return boolean
	*/
	public static function isCurrentlySupported($id_client)
	{
		if ($id_client === null) {
			throw new InvalidArguementException('Client Id cannot be null');
		}
		$sql = "select id_client from currently_supported where id_client
		= $id_client";
		return (bool)self::_retrieve($sql); 
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
