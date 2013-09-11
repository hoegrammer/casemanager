<?php

/* Class containing static methods for retrieving data
from the DB. */

class DataRetrieval 
{


	public static function getClientNameByCaseId($id_case) {
	$sql = "select concat(name_first, ' ', name_last) as name from lcm_case_client_org join lcm_client using (id_client)
		join lcm_case using (id_case) where id_case = $id_case";
		$array = self::_retrieve($sql);
                return $array[0]['name'];
	}

	public static function getAccompaniedBy($id_client) {
		$sql = "select regularly_accompanied_to_vulcan_house_by from lcm_client where id_client = $id_client";
		$array = self::_retrieve($sql);
		return $array[0][0];
	}
	/*
		Retrieves actual amount given and whether bus pass given
		for each client by week over the default period.
	*/
	public static function getWelfarePaymentHistories($from_helpdesk, $support_type)
	{
		$default_period = "5 week";
		$sql = "select id_client, name_first, name_last, outcome_amount,
		 bus_pass_given, date(date_start) as date_start from lcm_client join lcm_case_client_org 
		using (id_Client) join lcm_followup using (id_case) 
		join lcm_case using (id_case) left join lcm_faowelfaredesk using (id_client) where 
		date_start <= n() and date_start >= date_sub(curdate(), interval $default_period) 
		and type = 'followups27' and (bus_pass_given =1 or lcm_followup.outcome_amount > -1)";
		$sql .= self::_getWelfareFilters($from_helpdesk, $support_type); 
		$sql .= ' order by name_first';
		return self::_retrieve($sql);
	}

	/*
		Gets data about clients who are currently (financially)
		supported and have not had a welfare desk update today.
	*/
	public static function getAllCurrentlySupportedClientsNotUpdatedToday()
	{
		return self::_retrieve('select * from currently_supported
		where id_case not in (select id_case from lcm_followup
		where type = "followups27" and date(date_start) = date(now())) order by name_first');
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
	public static function getWelfareSheetInformation($from_helpdesk, $support_type)
	{
		$sql = "select id_client, name_first, name_last,
		currently_supported.amount as usual_amount, currently_supported.legal_reason, 
		lcm_faowelfaredesk.amount as fao_amount, bus_pass, letter, advocacy, from_helpdesk,
                note from currently_supported left join lcm_faowelfaredesk using (id_client) where 1 = 1";
		$sql .= self::_getWelfareFilters($from_helpdesk, $support_type); 
		$sql .= ' order by name_first';
                return self::_retrieve($sql);
	}

	/*
		Build the SQL for the filter(s) at runtime
	*/
	private static function _getWelfareFilters($from_helpdesk, $support_type)
	{
		$whereClause = "";
		if ($from_helpdesk === 1) {
			$filterSql .= " and  from_helpdesk = 1";
		} elseif ($from_helpdesk === 0) {
			$filterSql .= " and  (from_helpdesk = 0 or from_helpdesk is null)"; 
		}
		if ($support_type === 'accommodated') {
			$filterSql .= " and type_case = 'Accomidation'";
		} elseif ($support_type === 'not_accommodated') {
			$filterSql .= " and type_case != 'Accomidation'";
		}
		return $filterSql;
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
