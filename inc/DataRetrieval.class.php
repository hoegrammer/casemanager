<?php

/* Class containing static methods for retrieving data
from the DB. */

class DataRetrieval 
{

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
