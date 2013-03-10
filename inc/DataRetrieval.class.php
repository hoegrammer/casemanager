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
		Gets usual amount and bus pass for client. Fine if none

		@param int $id_client

		@return array - might be empty
	*/
	public static function getUsualSupportComboByClientId($id_client)
	{	
		if ($id_client === null) {
			throw new InvalidArguementException('Client Id cannot be null');
		}
		$sql = "select amount, legal_reason from currently_supported
		where id_client = $id_client";
		return self::_retrieve($sql);		
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
		Gets FAO Welfare Desk entry for this client.
		It is fine for there to be none
	
		@return array - might be empty
	*/
        public static function getFAOWelfareDeskByClientId($id_client)
	{
		$sql = "select amount, bus_pass, letter, advocacy, from_helpdesk,
		note from lcm_faowelfaredesk where id_client = $id_client";
		return self::_retrieve($sql);
	}

	/*
		For each currently supported client, gets:
			- Usual support amount + bus pass
			- FAO Welfare Desk information, if any
	*/
	public static function getWelfareSheetInformation()
	{
		$sql = "select name_first, name_last,
		currently_supported.amount as usual_amount, currently_supported.legal_reason, 
		lcm_faowelfaredesk.amount as fao_amount, bus_pass, letter, advocacy, from_helpdesk,
                note from currently_supported left join lcm_faowelfaredesk using (id_client)";
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
		while ($row = mysql_fetch_assoc($result)) {
			$data[] = $row;
		}
		return $data;
	}

}
