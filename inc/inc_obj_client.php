<?php

/*
	This file is part of the Legal Case Management System (LCM).
	(C) 2004-2005 Free Software Foundation, Inc.

	This program is free software; you can redistribute it and/or modify it
	under the terms of the GNU General Public License as published by the 
	Free Software Foundation; either version 2 of the License, or (at your 
	option) any later version.

	This program is distributed in the hope that it will be useful, but 
	WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
	or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
	for more details.

	You should have received a copy of the GNU General Public License along 
	with this program; if not, write to the Free Software Foundation, Inc.,
	59 Temple Place, Suite 330, Boston, MA  02111-1307, USA

	$Id: inc_obj_client.php,v 1.15 2006/11/14 19:15:47 mlutfy Exp $
*/

// Execute this file only once
if (defined('_INC_OBJ_CLIENT')) return;
define('_INC_OBJ_CLIENT', '1');

include_lcm('inc_obj_generic');
include_lcm('inc_db');
include_lcm('inc_contacts');

class LcmClient extends LcmObject {
	var $cases;
	var $case_start_from;
	private $_id_client;

	function getId() {
		return $this->_id_client;
	}

	function getAccompaniedBy() {
		return DataRetrieval::getAccompaniedBy($this->_id_client);
	}

	function LcmClient($id_client = 0, $record=0,$import=0) {
		$id_client = intval($id_client);
		$this->cases = null;
		$this->case_start_from = 0;

		$this->LcmObject();

		if ($import > 0)
			{
			$query = "SELECT * FROM lcm_import_client WHERE id_client = $import";
			$result = lcm_query($query);

			if (($row = lcm_fetch_array($result))) 
				foreach ($row as $key => $val) 
					$this->data[$key] = $val;
			$this->data['id_client']='0';
			}
		elseif ($record > 0)
			{
			$query = "SELECT * FROM lcm_old_client WHERE id_record = ".$record;
			$result = lcm_query($query);
			if (($row = lcm_fetch_array($result))) 
				foreach ($row as $key => $val) 
					$this->data[$key] = $val;
			}
		elseif ($id_client > 0) {
			$query = "SELECT * FROM lcm_client WHERE id_client = $id_client";
			$result = lcm_query($query);

			if (($row = lcm_fetch_array($result))) 
				foreach ($row as $key => $val) 
					$this->data[$key] = $val;
			}

		// If any, populate form values submitted
		foreach($_REQUEST as $key => $value) {
			$nkey = $key;

			if (substr($key, 0, 7) == 'client_')
				{
				$nkey = substr($key, 7);
				}

			$this->data[$nkey] = _request($key);
		}

		// If any, populate with session variables (for error reporting)
		if (isset($_SESSION['form_data'])) {
			foreach($_SESSION['form_data'] as $key => $value) {
				$nkey = $key;

				if (substr($key, 0, 7) == 'client_')
					{
					$nkey = substr($key, 7);
					}

				$this->data[$nkey] = _session($key);
			}
		}

		if (get_datetime_from_array($_SESSION['form_data'], 'date_birth', 'start', -1) != -1)
			$this->data['date_birth'] = get_datetime_from_array($_SESSION['form_data'], 'date_birth', 'start');
		$this->_id_client = $this->data['id_client'];
	}

	/*
		Says whether client is currently financially supported.

		@return boolean
	*/
	public function isCurrentlySupported()
	{
		return DataRetrieval::isCurrentlySupported($this->_id_client);
	}

	/*
		Retrieves the usual SupportCombo (amount and/or buss pass)
		for this client, if any

		@return SupportCombo or null
	*/
	public function getUsualSupportCombo()
	{
		$data = DataRetrieval::getUsualSupportComboByClientId($this->_id_client);
                if (empty($data)) {
                        return null;
                }
		// $data[0] because data comes in a multidimensional array 
		// which in fact contains a single row
		return new SupportCombo($data[0]['amount'], $data[0]['legal_reason']);
	}

	/*
		FAOWelfareDesk objects are created when the FAO Welfare Desk
		form on that client's file screen is submitted. If none exists
		for this client, create a default one using the client's usual
		support combo.

		@return FAOWelfareDesk or null
	*/
	public function getFAOWelfareDesk()
	{
		$data = DataRetrieval::getFAOWelfareDeskByClientId($this->_id_client);
		if (empty($data)) {
			// create a default FAO from the client's usual support combo
			$usualCombo = $this->getUsualSupportCombo();
			return $usualCombo->createFaoWelfareDesk($this->_id_client);
		}
		$data = $data[0]; // comes from db as multi array	
		return new FAOWelfareDesk(
			$this->_id_client, $data['amount'], $data['bus_pass'], $data['letter'],
			$data['advocacy'], $data['from_helpdesk'], $data['note']
		);
	}

	/* private */
	function loadCases($list_pos = 0) {
		global $prefs;
		//MATT WAS HERE, SHOW CASE "TYPE" INSTED OF TITLE ON CASE LIST IN "NEW CASE" PAGE
		$q = "SELECT clo.id_case, c.*, kw.title
				FROM lcm_case_client_org as clo, lcm_case as c
				LEFT JOIN lcm_keyword_case as kc ON c.id_case = kc.id_case
				LEFT JOIN lcm_keyword as kw ON kc.id_keyword = kw.id_keyword
				WHERE clo.id_client = " . $this->getDataInt('id_client', '__ASSERT__') . "
				AND clo.id_case = c.id_case ";

		// Sort cases by creation date
		$case_order = 'DESC';
		if (_request('case_order') == 'ASC' || _request('case_order') == 'DESC')
				$case_order = _request('case_order');
		
		$q .= " ORDER BY c.date_creation " . $case_order;

		$result = lcm_query($q);
		$number_of_rows = lcm_num_rows($result);
			
		if ($list_pos >= $number_of_rows)
			return;
				
		// Position to the page info start
		if ($list_pos > 0)
			if (!lcm_data_seek($result,$list_pos))
				lcm_panic("Error seeking position $list_pos in the result");

		if (lcm_num_rows($result)) {
			for ($cpt = 0; (($cpt < $prefs['page_rows']) && ($row = lcm_fetch_array($result))); $cpt++)
				array_push($this->cases, $row);
		}
	}

	function getCaseStart() {
		global $prefs;

		$start_from = _request('list_pos', 0);

		// just in case
		if (! ($start_from >= 0)) $start_from = 0;
		if (! $prefs['page_rows']) $prefs['page_rows'] = 10; 

		$this->cases = array();
		$this->case_start_from = $start_from;
		$this->loadCases($start_from);
	}

	function getCaseDone() {
		return ! (bool) (count($this->cases));
	}

	function getCaseIterator() {
		global $prefs;

		if ($this->getCaseDone())
			lcm_panic("LcmClient::getCaseIterator called but getCaseDone() returned true");

		$ret = array_shift($this->cases);

		if ($this->getCaseDone())
			$this->loadCases($start_from + $prefs['page_rows']);

		return $ret;
	}

	function getCaseTotal() {
		static $cpt_total_cache = null;

		if (is_null($cpt_total_cache)) {
			$query = "SELECT count(*) as cpt
					FROM lcm_case_client_org as clo, lcm_case as c
					WHERE clo.id_client = " . $this->getDataInt('id_client', '__ASSERT__') . "
					  AND clo.id_case = c.id_case ";

			$result = lcm_query($query);

			if (($row = lcm_fetch_array($result)))
				$cpt_total_cache = $row['cpt'];
			else
				$cpt_total_cache = 0;
		}

		return $cpt_total_cache;
	}

	function loadFollowups($list_pos = 0) {
		global $prefs;

		$q = "SELECT fu.id_followup, fu.date_start, fu.date_end, fu.type, fu.description, fu.case_stage, fu.bus_pass_given,
					fu.hidden, a.name_first, a.name_middle, a.name_last, c.type_case, fu.outcome_amount
				FROM lcm_followup as fu 
				LEFT JOIN lcm_case as c on c.id_case = fu.id_case
				LEFT JOIN lcm_case_client_org as cco on c.id_case = cco.id_case, lcm_author as a
				WHERE cco.id_client = " . $this->getDataInt('id_client', '__ASSERT__') . "
				AND a.id_author = fu.id_author";

		// Date filters (from interface)
		if (($date_start = get_datetime_from_array($this->data, 'date_start', 'start', -1)) != -1)
			$q .= " AND fu.date_start >= '$date_start' ";

		if (($date_end = get_datetime_from_array($this->data, 'date_end', 'start', -1)) != -1)
			$q .= " AND fu.date_end <= '$date_end' ";

		// Sort follow-ups by creation date
		$q .= " ORDER BY fu.date_start DESC, fu.id_followup DESC";// . $fu_order;

		$result = lcm_query($q);
		$number_of_rows = lcm_num_rows($result);
			
		if ($list_pos >= $number_of_rows)
			return;
				
		// Position to the page info start
		if ($list_pos > 0)
			if (!lcm_data_seek($result,$list_pos))
				lcm_panic("Error seeking position $list_pos in the result");

		if (lcm_num_rows($result)) {
			for ($cpt = 0; (($cpt < $prefs['page_rows'] || _request('list_pos') == 'all') && ($row = lcm_fetch_array($result))); $cpt++)
				array_push($this->followups, $row);
		}
	}

	function getFollowupStart() {
		global $prefs;

		$this->fu_start_from = _request('list_pos', 0);

		// just in case
		if (! ($this->fu_start_from >= 0)) $this->fu_start_from = 0;
		if (! $prefs['page_rows']) $prefs['page_rows'] = 10; 

		$this->followups = array();

		$this->loadFollowups($this->fu_start_from);
	}

	function getFollowupDone() {
		return ! (bool) (count($this->followups));
	}

	function getFollowupIterator() {
		global $prefs;

		if ($this->getFollowupDone())
			lcm_panic("LcmClient::getFollowupIterator called but getFollowupDone() returned true");

		return array_shift($this->followups);
	}

	function getFollowupTotal() {
		static $cpt_total_cache = null;

		if (is_null($cpt_total_cache)) {
			$query = "SELECT count(*) as cpt
						FROM lcm_followup as fu 
						LEFT JOIN lcm_case as c on c.id_case = fu.id_case 
						LEFT JOIN lcm_case_client_org as cco on cco.id_case = c.id_case, lcm_author as a
						WHERE id_client = " . $this->data['id_client'] . "
						  AND fu.id_author = a.id_author ";

			$result = lcm_query($query);

			if (($row = lcm_fetch_array($result)))
				$cpt_total_cache = $row['cpt'];
			else
				$cpt_total_cache = 0;
		}

		return $cpt_total_cache;
	}

	function getName() {
		return get_person_name($this->data);
	}

	function validate() {
		$errors = array();

		if (! $this->getDataString('name_first'))
			$errors['name_first'] = _Ti('person_input_name_first') . _T('warning_field_mandatory');

		if (! $this->getDataString('name_last'))
			$errors['name_last'] = _Ti('person_input_name_last') . _T('warning_field_mandatory');

		if (read_meta('client_name_middle') == 'yes_mandatory' && (!$this->getDataString('name_middle')))
			$errors['name_middle'] = _Ti('person_input_name_middle') . _T('warning_field_mandatory');

		if (read_meta('client_citizen_number') == 'yes_mandatory' && (!$this->getDataString('citizen_number')))
			$errors['citizen_number'] = _Ti('person_input_citizen_number') . _T('warning_field_mandatory');

		if (read_meta('client_civil_status') == 'yes_mandatory' && (!$this->getDataString('civil_status')))
			$errors['civil_status'] = _Ti('person_input_civil_status') . _T('warning_field_mandatory');

		if (read_meta('client_income') == 'yes_mandatory' && (!$this->getDataString('income')))
			$errors['income'] = _Ti('person_input_income') . _T('warning_field_mandatory');

		$genders = array('unknown' => 1, 'female' => 1, 'male' => 1);

		if (! array_key_exists($this->getDataString('gender'), $genders))
			$errors['gender'] = _Ti('person_input_gender') . 'Incorrect format.'; // TRAD FIXME

		//
		// Custom validation functions
		//

		// * Client name (special function)
		if (include_validator_exists('client_name')) {
			include_validator('client_name');
			$foo = new LcmCustomValidateClientName();

			$test = array('first', 'last');
			
			if (substr(read_meta('client_name_middle'), 0, 3) == 'yes')
				array_push($test, 'middle');

			foreach ($test as $t) {
				$n = $this->getDataString('name_' . $t);

				if ($err = $foo->validate($this->getDataInt('id_client'), $t, $n))
					$errors['name_' . $t] = _Ti('person_input_name_' . $t) . $err;
			}
		}

		// * other fields
		$id_client = $this->getDataInt('id_client');

		$fields = array('citizen_number' => 'ClientCitizenNumber', 
					'civil_status' => 'ClientCivilStatus',
					'income' => 'ClientIncome', 
					'gender' => 'PersonGender');

		foreach ($fields as $f => $func) {
			if (include_validator_exists($f)) {
				include_validator($f);
				$class = "LcmCustomValidate$func";
				$data = $this->getDataString($f);
				$v = new $class();

				if ($err = $v->validate($id_client, $data)) 
					$errors[$f] = _Ti('person_input_' . $f) . $err;
			}
		}

		return $errors;
	}

	//
	// Save client record in DB (create/update)
	// Returns array of errors, if any
	//

	function save() {
		$errors = $this->validate();

		if (count($errors))
			return $errors;
		if ($this->getDataInt('id_client'))
			{
			//copy client details to old client file.
			lcm_query('insert into lcm_old_client select *, ""as id_record from lcm_client where id_client = '.$this->getDataInt('id_client'));
			}

		//
		// Update record in database
		//
		$cl = "name_first = '"  . clean_input($this->getDataString('name_first')) . "',
			   name_middle = '" . clean_input($this->getDataString('name_middle')) . "',
			   name_last = '"   . clean_input($this->getDataString('name_last')) . "',
			   gender = '"      . clean_input($this->getDataString('gender')) . "',
			   notes = '"       . clean_input($this->getDataString('notes')) . "'"; // , 

		if ($this->getDataString('date_birth'))
			$cl .= ", date_birth = '" . $this->getDataString('date_birth') . "'";
	
		if (clean_input($this->getDataString('citizen_number')))
			$cl .= ", citizen_number = '" . clean_input($this->getDataString('citizen_number')) . "'";
	
		//MATT WAS HERE! SAVE NASS NUMBER TO DB WHEN ENTERED IN NEW CLIENT/ EDIT CLIENT PAGES.	
		if (clean_input($this->getDataString('nass_number')))
			$cl .= ", nass_number = '" . clean_input($this->getDataString('nass_number')) . "'";

		if (clean_input($this->getDataString('check1')))
			$cl .= ", check1=1";
		else
			$cl .= ", check1=0";
		if (clean_input($this->getDataString('check2')))
			$cl .= ", check2=1";
		else
			$cl .= ", check2=0";
		if (clean_input($this->getDataString('check3')))
			$cl .= ", check3=1";
		else
			$cl .= ", check3=0";
		if (clean_input($this->getDataString('check4')))
			$cl .= ", check4=1";
		else
			$cl .= ", check4=0";

		if (clean_input($this->getDataString('country')))
			$cl .= ", country = '" . clean_input($this->getDataString('country')) . "'";
		
		if (clean_input($this->getDataString('language')))
			$cl .= ", language = '" . clean_input($this->getDataString('language')) . "'";
		
		if (clean_input($this->getDataString('eng_level')))
			$cl .= ", eng_level = '" . clean_input($this->getDataString('eng_level')) . "'";
		
		if (clean_input($this->getDataString('intrepreter')))
			$cl .= ", intrepreter = '" . clean_input($this->getDataString('intrepreter')) . "'";
		
		if (clean_input($this->getDataString('religion')))
			$cl .= ", religion = '" . clean_input($this->getDataString('religion')) . "'";

		if ($this->getDataString('doe')=='yes')
			{
			$day = $this->getDataString('zot_day');
			$month = $this->getDataString('zot_month');
			$year = $this->getDataString('zot_year');
			if (!$day)
				$day='00';
			if (!$month)
				$month='00';
			if (!$year)
				$year='00';
			$cl .= ", entery_date = '". $year ."-". $month . "-" . $day ." 00:00:00'";

			}
		
		if (clean_input($this->getDataString('status')))
			$cl .= ", status = '" . clean_input($this->getDataString('status')) . "'";
		
		if (clean_input($this->getDataString('status_details')))
			$cl .= ", status_details = '" . clean_input($this->getDataString('status_details')) . "'";
		
		if (clean_input($this->getDataString('referal')))
			$cl .= ", referal = '" . clean_input($this->getDataString('referal')) . "'";
		
		if (clean_input($this->getDataString('referal_details')))
			$cl .= ", referal_details = '" . clean_input($this->getDataString('referal_details')) . "'";
		
		if (clean_input($this->getDataString('national_assistance')))
			$cl .= ", national_assistance = '" . clean_input($this->getDataString('national_assistance')) . "'";
		
		if (clean_input($this->getDataString('address')))
			$cl .= ", address = '" . clean_input($this->getDataString('address')) . "'";
		
		if (clean_input($this->getDataString('telephone')))
			$cl .= ", telephone = '" . clean_input($this->getDataString('telephone')) . "'";
		
		if (clean_input($this->getDataString('contact')))
			$cl .= ", contact = '" . clean_input($this->getDataString('contact')) . "'";
		
		if (clean_input($this->getDataString('section4')))
			$cl .= ", section4 = '" . clean_input($this->getDataString('section4')) . "'";
		
		if (clean_input($this->getDataString('income')))
			$cl .= ", income = '" . clean_input($this->getDataString('income')) . "'";
		
		if (clean_input($this->getDataString('income_notes')))
			$cl .= ", income_notes = '" . clean_input($this->getDataString('income_notes')) . "'";

		if (clean_input($this->getDataString('accomidation')))
			$cl .= ", accomidation = '" . clean_input($this->getDataString('accomidation')) . "'";
		
		if (clean_input($this->getDataString('accomidation_notes')))
			$cl .= ", accomidation_notes = '" . clean_input($this->getDataString('accomidation_notes')) . "'";

		if (clean_input($this->getDataString('food')))
			$cl .= ", food = '" . clean_input($this->getDataString('food')) . "'";
		
		if (clean_input($this->getDataString('food_notes')))
			$cl .= ", food_notes = '" . clean_input($this->getDataString('food_notes')) . "'";
		if ($this->getDataString('social')=='yes')
			{
			if ($this->getDataString('social1')=='on')
				$cl .= ", social1 = '1'";
			else
				$cl .= ", social1 = '0'";
			if ($this->getDataString('social2')=='on')
				$cl .= ", social2 = '1'";
			else
				$cl .= ", social2 = '0'";
			if ($this->getDataString('social3')=='on')
				$cl .= ", social3 = '1'";
			else
				$cl .= ", social3 = '0'";
			if ($this->getDataString('social4')=='on')
				$cl .= ", social4 = '1'";
			else
				$cl .= ", social4 = '0'";
			if ($this->getDataString('social5')=='on')
				$cl .= ", social5 = '1'";
			else
				$cl .= ", social5 = '0'";
			}
		
		if (clean_input($this->getDataString('social_notes')))
			$cl .= ", social_notes = '" . clean_input($this->getDataString('social_notes')) . "'";
		
		if (clean_input($this->getDataString('health_p')))
			$cl .= ", health_p = '" . clean_input($this->getDataString('health_p')) . "'";
		
		if (clean_input($this->getDataString('health_m')))
			$cl .= ", health_m = '" . clean_input($this->getDataString('health_m')) . "'";
		
		if (clean_input($this->getDataString('medications')))
			$cl .= ", medications = '" . clean_input($this->getDataString('medications')) . "'";
		
		if (clean_input($this->getDataString('hospital')))
			$cl .= ", hospital = '" . clean_input($this->getDataString('hospital')) . "'";
		
		if (clean_input($this->getDataString('clinics')))
			$cl .= ", clinics = '" . clean_input($this->getDataString('clinics')) . "'";

		if (clean_input($this->getDataString('solicitor')))
			$cl .= ", solicitor = '" . clean_input($this->getDataString('solicitor')) . "'";

		if (clean_input($this->getDataString('gp')))
			$cl .= ", gp = '" . clean_input($this->getDataString('gp')) . "'";
		
		if (clean_input($this->getDataString('accom')))
			$cl .= ", accom = " . clean_input($this->getDataString('accom'));

		if (clean_input($this->getDataString('pannel')))
			$cl .= ", pannel = '" . clean_input($this->getDataString('pannel')) . "'";

		if (clean_input($this->getDataString('civil_status')))
			$cl .= ", civil_status = '" . clean_input($this->getDataString('civil_status')) . "'";
	
//		if (clean_input($this->getDataString('income')))
//			$cl .= ", income = '" . clean_input($this->getDataString('income')) . "'";
	
		if ($this->getDataInt('id_client') > 0) {
			$q = "UPDATE lcm_client
				SET 
					date_update = '".$this->getDataString('date_update')."',
					$cl 
				WHERE id_client = " . $this->getDataInt('id_client', '__ASSERT__');
			lcm_query($q);
		} else {
			$q = "INSERT INTO lcm_client
					SET date_creation = NOW(),
						date_update = '".$this->getDataString('date_update')."',
						$cl";
	
			$result = lcm_query($q);
			$this->data['id_client'] = lcm_insert_id('lcm_client', 'id_client');
		}

		// Keywords
		update_keywords_request('client', $this->getDataInt('id_client'));
		
		if ($_SESSION['errors'])
			$errors = array_merge($_SESSION['errors'], $errors);
		
		// Insert/update client contacts
		include_lcm('inc_contacts');
		update_contacts_request('client', $this->getDataInt('id_client'));
		
		if ($_SESSION['errors'])
			$errors = array_merge($_SESSION['errors'], $errors);
		
		return $errors;
	}






	function printFollowupsTitle()
		{
//		show_page_subtitle("Case Work in ".$this->getDataString('name_first')."'s File");
		}

	function printFollowups($show_filters = false) {
		$cpt = 0;
		$my_list_pos = intval(_request('list_pos', 0));
		// Show filters (if not shown in ajaxed page)
		$show_filters = 0;
		if ($show_filters) {
			// By default, show from "case creation date" to NOW().
			$link = new Link();
			$link->delVar('date_start_day');
			$link->delVar('date_start_month');
			$link->delVar('date_start_year');
			$link->delVar('date_end_day');
			$link->delVar('date_end_month');
			$link->delVar('date_end_year');
			echo $link->getForm();

			$date_end = get_datetime_from_array($_REQUEST, 'date_end', 'end', '0000-00-00 00:00:00'); // date('Y-m-d H:i:s'));
			$date_start = get_datetime_from_array($_REQUEST, 'date_start', 'start', '0000-00-00 00:00:00'); // $row['date_creation']);

			echo _Ti('time_input_date_start');
			echo get_date_inputs('date_start', $date_start);

			echo _Ti('time_input_date_end');
			echo get_date_inputs('date_end', $date_end);
			echo ' <button name="submit" type="submit" value="submit" class="simple_form_btn">' . _T('button_validate') . "</button>\n";
			echo "</form>\n";

			echo "<div style='margin-bottom: 4px;'>&nbsp;</div>\n"; // FIXME patch for now (leave small space between filter and list)
		}

		show_listfu_start('general', false);

		for ($cpt = 0, $this->getFollowupStart(); (! $this->getFollowupDone()); $cpt++) {
			$item = $this->getFollowupIterator();
			show_listfu_item($item, $cpt);
		}

		if (! $cpt)
			echo "No work in this file yet"; // TRAD

		show_list_end($my_list_pos, $this->getFollowupTotal(), true);
	}





}

class LcmClientInfoUI extends LcmClient {
	function LcmClientInfoUI($id_client = 0,$record=0,$import=0) {
		$this->LcmClient($id_client,$record,$import);
	}

	function printGeneral1($show_subtitle = true) {
		$meta_citizen_number = read_meta('client_citizen_number');
		$meta_civil_status = read_meta('client_civil_status');
		$meta_income = read_meta('client_income');
		$meta_date_birth = read_meta('client_date_birth');

		if ($show_subtitle)
			show_page_subtitle(_T('generic_subtitle_general'), 'clients_intro');

		echo '<ul class="info">';
		echo '<li>' 
			. '<span class="label2">' . _Ti('client_input_id') . '</span>'
			. '<span class="value2">' . $this->getDataInt('id_client') . '</span>'
			. "</li>\n";

		echo '<li>'
			. '<span class="label1">' . _Ti('person_input_name') . '</span>'
			. '<span class="value1">' . $this->getName() . '</span>'
			. "</li>\n";

		if ($this->data['gender'] == 'male' || $this->getDataString('gender') == 'female')
			$gender = _T('person_input_gender_' . $this->getDataString('gender'));
		else
			$gender = _T('info_not_available');

		if (substr($meta_date_birth, 0, 3) == 'yes')
			echo '<li>'
				. '<span class="label2">Date of Birth: </span>'
				. '<span class="value2">'
				. format_date($this->getDataString('date_birth')) 
				. " (" . _T('person_info_years_old', array('years' => years_diff($this->getDataString('date_birth')))) . ")"
				. '</span>'
				. "</li>\n";

		echo '<li>'
			. '<span class="label2">' . _Ti('person_input_gender') . '</span>'
			. '<span class="value2">' . $gender . '</span>'
			. "</li>\n";

		if (substr($meta_citizen_number, 0, 3) == 'yes')
			echo '<li>'
				. '<span class="label1">' . _Ti('person_input_citizen_number') . '</span>'
				. '<span class="value1">' . clean_output($this->getDataString('citizen_number')) . '</span>'
				. "</li>\n";

		//MATT WAS HERE, PRINT "NASS NUMBER" ON THE CLIENT DETAILS PAGE. FETCHED MAGICLY BY SELECT *
		echo '<li>'
			. '<span class="label1">' . _Ti('person_input_nass_number') . '</span>'
			. '<span class="value1">' . clean_output($this->getDataString('nass_number')) . '</span>'
			. "</li>\n";

		
		$i = get_kw_from_name('ethnicity',$this->getDataString('country'));
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Country of Origin: </span>'
				. '<span class="value1">' .remove_number_prefix($i['title']) . '</span>'
				. "</li>\n";
			}

		$i = get_kw_from_name('language',$this->getDataString('language'));
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Language: </span>'
				. '<span class="value1">' .remove_number_prefix($i['title']) . '</span>'
				. "</li>\n";
			}

		$i = get_kw_from_name('english',$this->getDataString('eng_level'));
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">English Level: </span>'
				. '<span class="value1">' .remove_number_prefix($i['title']) . '</span>'
				. "</li>\n";
			}

		$i = get_kw_from_name('interpreter',$this->getDataString('intrepreter'));
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Interpreter Needed: </span>'
				. '<span class="value1">' .remove_number_prefix($i['title']) . '</span>'
				. "</li>\n";
			}

		$i = get_kw_from_name('religion',$this->getDataString('religion'));
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Religion: </span>'
				. '<span class="value1">' .remove_number_prefix($i['title']) . '</span>'
				. "</li>\n";
			}

		$i = format_date($this->getDataString('entery_date'),'date_short');
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Date of Entry: </span>'
				. '<span class="value1">' .$i . '</span>'
				. "</li>\n";
			}

		$i = get_kw_from_name('status',$this->getDataString('status'));
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Status: </span>'
				. '<span class="value1">' .remove_number_prefix($i['title']) . '</span>'
				. "</li>\n";
			}
		$i = $this->getDataString('status_details');
		if ($i != '');
			{
			echo '<li>'
				. '<span class="label1">Status notes: </span>'
				. '<span class="value2">' .$i. '</span>'
				. "</li>\n";
			}

		$i = get_kw_from_name('clientreferal',$this->getDataString('referal'));
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Referal: </span>'
				. '<span class="value1">' .remove_number_prefix($i['title']) . '</span>'
				. "</li>\n";
			}
		$i = $this->getdatastring('referal_details');
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Referal notes: </span>'
				. '<span class="value2">' .$i. '</span>'
				. "</li>\n";
			}

		$i = $this->getdatastring('solicitor');
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Solicitor\'s details: </span>'
				. '<span class="value1">' .$i. '</span>'
				. "</li>\n";
			}

		$i = get_kw_from_name('national_assistance',$this->getdatastring('national_assistance'));
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">National assistance: </span>'
				. '<span class="value1">' .remove_number_prefix($i['title']) . '</span>'
				. "</li>\n";
			}

		$i = get_kw_from_name('section4',$this->getdatastring('section4'));
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Section 4: </span>'
				. '<span class="value1">' .remove_number_prefix($i['title']) . '</span>'
				. "</li>\n";
			}

		$i = get_kw_from_name('other_income',$this->getdatastring('income'));
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Other Income: </span>'
				. '<span class="value1">' .remove_number_prefix($i['title']) . '</span>'
				. "</li>\n";
			}

		$i = $this->getdatastring('income_notes');
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Income details: </span>'
				. '<span class="value2">' .$i. '</span>'
				. "</li>\n";
			}

		$i = get_kw_from_name('accomidation',$this->getdatastring('accomidation'));
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Accommodation: </span>'
				. '<span class="value1">' .remove_number_prefix($i['title']) . '</span>'
				. "</li>\n";
			}

		$i = $this->getdatastring('accomidation_notes');
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Accommodation details: </span>'
				. '<span class="value2">' .$i. '</span>'
				. "</li>\n";
			}

		$i = get_kw_from_name('food',$this->getdatastring('food'));
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Food: </span>'
				. '<span class="value1">' .remove_number_prefix($i['title']) . '</span>'
				. "</li>\n";
			}


		$i = $this->getdatastring('food_notes');
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Food details: </span>'
				. '<span class="value2">' .$i. '</span>'
				. "</li>\n";
			}

		echo '<li>'
			. '<span class="label1">Social Support: </span>'
			. '<span class="value1">'
			. ($this->getDataInt('social1')==1?"Family, ":"")
			. ($this->getDataInt('social2')==1?"Friends, ":"")
			. ($this->getDataInt('social3')==1?"Other Community Members, ":"")
			. ($this->getDataInt('social4')==1?"People met through projects, ":"")
			. ($this->getDataInt('social5')==1?"Other people, ":"")
			. '</span>'
			. "</li>\n";

		$i = $this->getdatastring('social_notes');
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Social support details: </span>'
				. '<span class="value2">' .$i. '</span>'
				. "</li>\n";
			}


		$i = $this->getdatastring('health_p');
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Pysical problems: </span>'
				. '<span class="value1">' .$i. '</span>'
				. "</li>\n";
			}

		$i = $this->getdatastring('health_m');
		if ($i);
			{
			echo '<li>'
				. '<span class="label1">Psychological / psychiatric problems: </span>'
				. '<span class="value1">' .$i. '</span>'
				. "</li>\n";
			}

		$i = $this->getdatastring('medications');
		if (!$i=="");
			{
			echo '<li>'
				. '<span class="label1">Regular medications: </span>'
				. '<span class="value1">' .$i. '</span>'
				. "</li>\n";
			}

		
		$i = $this->getdatastring('hopstial');
		if (!$i=="");
			{
			echo '<li>'
				. '<span class="label1">Admissions to Hospital: </span>'
				. '<span class="value1">' .$i. '</span>'
				. "</li>\n";
			}

		$i = $this->getdatastring('clinics');
		if (!$i=="");
			{
			echo '<li>'
				. '<span class="label1">Hospital clinics attended: </span>'
				. '<span class="value1">' .$i. '</span>'
				. "</li>\n";
			}

		$i = $this->getdatastring('gp');
		if (!$i=="");
			{
			echo '<li>'
				. '<span class="label1">GP\' details: </span>'
				. '<span class="value1">' .$i. '</span>'
				. "</li>\n";
			}

//		if (substr($meta_civil_status, 0, 3) == 'yes') {
//			// [ML] Patch for bug #1372138 (LCM < 0.6.4)
//			$civil_status = $this->getDataString('civil_status', 'unknown');
//
//			echo '<li>'
///				. '<span class="label2">' . _Ti('person_input_civil_status') . '</span>'
//				. '<span class="value2">' . _Tkw('civilstatus', $civil_status) . '</span>'
//				. "</li>\n";
//		}
//
//		if (substr($meta_income, 0, 3) == 'yes') {
//			// [ML] Patch for bug #1372138 (LCM < 0.6.4)
//			$income = $this->getDataString('income', 'unknown');
//
//			echo '<li>' 
//				. '<span class="label2">' . _Ti('person_input_income') . '</span>'
//				. '<span class="value2">' . _Tkw('income', $income) . '</span>'
//				. "</li>\n";
//		}
//
//








//		show_all_keywords('client', $this->getDataInt('id_client'));

		echo '<li>'
			. '<span class="label2">' . _Ti('case_input_date_creation') . '</span>'
			. '<span class="value2">' . format_date($this->getDataString('date_creation')) . '</span>'
			. "</li>\n";

		echo '<li class="large">'
			. '<span class="label2">' . _Ti('client_input_notes') . '</span>' 
			. '<span class="value2">'. nl2br(clean_output($this->getDataString('notes'))) . '</span>'
			. "</li>\n";

		echo '<li>'
			. '<span class="label1"> Housing Suitability: </span>'
			. '<span class="value2">' 
			. 'Priviate Home: <b>'. ($this->getDataString('check1')?"Yes":"No") . '</b>, '
			. 'Shared House: <b>'. ($this->getDataString('check2')?"Yes":"No") . '</b>, '
			. 'Nightshelter: <b>'. ($this->getDataString('check3')?"Yes":"No") . '</b>. '
			. '</li>';
		echo '<li>'
			. '<span class="label1"> Accomidation Priority: </span>'
			. '<span class="value1">' . $this->getDataString('accom')
			. '</li>';
		if ($this->getDataString('pannel'))
			{
			echo '<li>'
				. '<span class="label1"> Pannel Score: </span>'
				. '<span class="value1">' . $this->getDataString('pannel')
				. '</li>';
			}
		echo "</ul>\n";
		// Show client contacts (if any)
		show_all_contacts('client', $this->getDataInt('id_client'));

	}

	function printAttach() {
		echo '<input type="hidden" name="attach_client" value="' . $this->getDataInt('id_client', '__ASSERT__') . '" />' . "\n";
	}

	//MATT WAS HERE. EDIT THIS FUNCTION, ONLY DISPLAY THE LISTCASE_START/LISTCASE_END IF SOME CASES WILL BE SHOWN. FACILICATED WITH $MATT
	function printCases($find_case_string = '') {
		$cpt = 0;
		$my_list_pos = intval(_request('list_pos', 0));
		$matt=false;
		for ($cpt = 0, $this->getCaseStart(); (! $this->getCaseDone()); $cpt++) {
			if (!$matt)
				{
				show_page_subtitle(_T('client_subtitle_cases_warning'), 'cases_participants');
				echo "<p class=\"normal_text\">\n";
				lcm_bubble('duplicate_case');
				show_listcase_start();
				$matt=true;
				}
			$item = $this->getCaseIterator();
			show_listcase_item($item, $cpt);//, $find_case_string, "","");// 'javascript:;', 'onclick="getCaseInfo(' . $item['id_case'] . ')"');
		}

		if ($matt)
			{
			show_listcase_end($my_list_pos, $this->getCaseTotal());	
			echo "</fieldset>\n";
			}
	}

	function getDropdown($groupname, $varname)
		{
		if ($varname =='')
			$varname=$groupname;
		$default_fu = get_suggest_in_group_name($groupname);
		$futype_kws = get_keywords_in_group_name($groupname);
		$kw_found = false;
		$matt = $this->getDataString($varname);
		foreach($futype_kws as $kw) 
			{
//			print"(".$kw['name']."|".$default_fu.")";
			$sel = isSelected(($kw['name'] == $default_fu)||($kw['name']==$matt));
			if ($sel) $kw_found = true;
//			$sel = isSelected($matt == $kw['name']);
//			if ($sel) $kw_found = true;
			echo '<option value="' . $kw['name'] . '"' . $sel . '>' . _T(remove_number_prefix($kw['title'])) . "</option>\n";
//			print " | ";
			}
		// Exotic case where the FU keyword was hidden by the administrator,
		// but an old follow-up using that keyword is being edited.
		if (! $kw_found)
			{
//			echo '<option selected="selected" value="' . $default_fu . '">' . _Tkw('followups', $default_fu) . "</option>\n";
			}

		}
//	function getCurrent($groupname, $varname)
//		{
//		if ($varname =='')
//			$varname=$groupname;
//		$default_fu = get_suggest_in_group_name($groupname);
//		$futype_kws = get_keywords_in_group_name($groupname);
//		$kw_found = false;
//		$matt = $this->getDataString($varname);
//		foreach($futype_kws as $kw) 
//			{
//			print"(".$kw['name']."|".$default_fu.")";
//			$sel = isSelected(($kw['name'] == $default_fu)||($kw['name']==$matt));
//			if ($sel) $kw_found = true;
//			$sel = isSelected($matt == $kw['name']);
//			if ($sel) $kw_found = true;
//			echo '<option value="' . $kw['name'] . '"' . $sel . '>' . _T(remove_number_prefix($kw['title'])) . "</option>\n";
//			print " | ";
//			}
//		// Exotic case where the FU keyword was hidden by the administrator,
//		// but an old follow-up using that keyword is being edited.
//		if (! $kw_found)
//			{
//			echo '<option selected="selected" value="' . $default_fu . '">' . _Tkw('followups', $default_fu) . "</option>\n";
//			}
//
//		}

	function printGeneral($mode) {
		// Get site preferences
		$client_name_middle = read_meta('client_name_middle');
		$client_citizen_number = read_meta('client_citizen_number');
		$client_civil_status = read_meta('client_civil_status');
		$client_income = read_meta('client_income');
		$meta_date_birth = read_meta('client_date_birth');
		if ($mode!='salrev')
			echo '<table width="99%" border="0" align="center" cellpadding="5" cellspacing="0" class="tbl_usr_dtl">' . "\n";
		
		if ($mode!='scores')
			{
//			echo '<tr><td colspan="2">';show_page_subtitle('Personal Infomation');echo'</td></tr>';
			if($this->getDataInt('id_client')) 
				{
				echo "<tr><td>" . _T('client_input_id') . "</td>\n";
				echo "<td>" . $this->getDataInt('id_client')
					. '<input type="hidden" name="id_client" value="' . $this->getDataInt('id_client') . '" /></td></tr>' . "\n";
				}

			// Client name
			echo '<tr><td>' ._T('person_input_name_first') . '</td>' . "\n";
			echo '<td>'. clean_output($this->getDataString('name_first')) . '</td></tr>' . "\n";
			
//	// [ML] always show middle name, if any, no matter the configuration
//	if ($this->getDataString('name_middle') || substr($client_name_middle, 0, 3) == 'yes') 
//		{
//		echo '<tr><td>' . f_err_star('name_middle') . _T('person_input_name_middle') . '</td>' . "\n";
//		echo '<td><input name="name_middle" value="' . clean_output($this->getDataString('name_middle')) . '" class="search_form_txt" /></td></tr>' . "\n";
//		}
				
			echo '<tr><td>' . _T('person_input_name_last') . '</td>' . "\n";
			echo '<td>' . clean_output($this->getDataString('name_last')) . '</td></tr>' . "\n";
			
			if (substr($meta_date_birth, 0, 3) == 'yes') 
				{
				echo "<tr>\n";
				echo "<td>" . _Ti('person_input_date_birth') . "</td>\n";
				echo "<td>" 
					. format_date($this->getDataString('date_birth'),'date_short')
					. "</td>\n";
				echo "</tr>\n";
				}
			
			echo '<tr><td>' . f_err_star('gender') . _T('person_input_gender') . '</td>' . "\n";
			echo '<td>';

			echo $this->getDataString('gender');
			
			echo "</td></tr>\n";
			
//			if ($this->getDataString('id_client')) 
//				{
//				echo "<tr>\n";
//				echo '<td>' . _Ti('time_input_date_creation') . '</td>';
//				echo '<td>' . format_date($this->getDataString('date_creation'), 'full') . '</td>';
//				echo "</tr>\n";
//				}
			
			if (substr($client_citizen_number, 0, 3) == 'yes') 
				{
				echo "<tr>\n";
				echo '<td>' .  _T('person_input_citizen_number') . '</td>';
				echo '<td>' .  clean_output($this->getDataString('citizen_number')) . '</td>';
				echo "</tr>\n";
				}
			
			//MATT WAS HERE, ADDING NASS NUMBER TO "INPUT CLIENT"/"EDIT CLIENT" PAGES. THIS AUTOMAGICLY PROPERGATES THANKS TO SELECT * QUERY ABOVE.
			
			echo "<tr>\n";
			echo '<td>' . _T('person_input_nass_number') . '</td>';
			echo '<td>' .  clean_output($this->getDataString('nass_number')) . '</td>';
			echo "</tr>\n";

			// +-------------+
			// | COUNTRY     |
			// +-------------+
			echo '<tr><td>';
			echo 'Country of Origin';
			echo '</td><td>';
//			echo '<select name="country" size="1" class="sel_frm">';
			$x = get_kw_from_name('ethnicity',$this->getDataString('country'));
			print remove_number_prefix($x['title']);
//			echo "</select>\n";
			echo '</td></tr>';
			// +-------------+
			// | LANGUAGE    |
			// +-------------+
			echo '<tr><td>';
			echo 'Language';
			echo '</td><td>';
			$x = get_kw_from_name('language',$this->getDataString('language'));
			print remove_number_prefix($x['title']);
			echo '</td></tr>';
			// +-------------+
			// | ENG_LEVEL   |
			// +-------------+
			echo '<tr><td>';
			echo 'English Level';
			echo '</td><td>';
			$x = get_kw_from_name('english',$this->getDataString('eng_level'));
			print remove_number_prefix($x['title']);
			echo '</td></tr>';
			// +-------------+
			// | INTERPRETER |
			// +-------------+
			echo '<tr><td>';
			echo 'Interpreter Required';
			echo '</td><td>';
			$x = get_kw_from_name('interpreter',$this->getDataString('intrepreter'));
			print remove_number_prefix($x['title']);
			echo '</td></tr>';
			// +-------------+
			// | RELIGION    |
			// +-------------+
			echo '<tr><td>';
			echo 'Religion';
			echo '</td><td>';
			$x = get_kw_from_name('religion',$this->getDataString('religion'));
			print remove_number_prefix($x['title']);
			echo '</td></tr>';
			// +---------------+
			// | DATE OF ENTRY |
			// +---------------+
			echo '<input type="hidden" name="doe" value="yes"/>';
			echo '<tr><td>';
			echo 'Date of Entry';
			echo '</td><td>';
			$the_date=date('Y-m-d');
//			echo get_date_inputs('zot',$this->getDataString('entery_date'));
			echo format_date($this->getDataString('entery_date'),'date_short');

			echo '</td></tr>';
			// +-------------+
			// | STATUS      |
			// +-------------+
			echo '<tr><td>';
			echo 'Status';
			echo '</td><td>';
			$x = get_kw_from_name('status',$this->getDataString('status'));
			print remove_number_prefix($x['title']);
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Status Details';
			echo '</td><td>';
			echo '<textarea disabled name="status_details" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('status_details'))
				. '</textarea>';
			echo '</td></tr>';

			// +-------------+
			// | REFERAL     |
			// +-------------+
			echo '<tr><td>';
			echo 'Referal';
			echo '</td><td>';
			$x = get_kw_from_name('clientreferal',$this->getDataString('referal'));
			print remove_number_prefix($x['title']);
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Referal Details';
			echo '</td><td>';
			echo '<textarea disabled name="referal_details" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('referal_details'))
				. '</textarea>';
			echo '</td></tr>';

//			// +-------------+
//			// | LETTERS     |
//			// +-------------+
//			echo '<tr><td>';
//			echo 'Documentation';
//			echo '</td><td>';
//			echo '<select name="referal" size="1" class="sel_frm">';
//			$x=$this->getDropdown('letter');
//			echo "</select>\n";
//			echo '</td></tr>';


			echo '<tr><td colspan="2">';show_page_subtitle('Contact Details');echo'</td></tr>';
			echo '<tr><td>Address:</td>' . "\n";
			echo '<td>'.clean_output($this->getDataString('address')).'</td></tr>' . "\n";
			echo '<tr><td>Telephone:</td>' . "\n";
			echo '<td>'.clean_output($this->getDataString('telephone')) .'</td></tr>' . "\n";
			echo '<tr><td>';
			echo 'Other Contacts:';
			echo '</td><td>';
			echo '<textarea disabled name="contact" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('contact'))
				. '</textarea>';
			echo '</td></tr>';



			echo '<tr><td colspan="2">';show_page_subtitle('Solicitor');echo'</td></tr>';
			// +-------------+
			// | Solicitor   |
			// +-------------+
			echo '<tr><td>';
			echo 'Solicitor\'s details';
			echo '</td><td>';
			echo '<textarea disabled name="solicitor" id="input_status_notes" class="frm_tarea" rows="4" cols="60">'
				. clean_output($this->getDataString('solicitor'))
				. '</textarea>';
			echo '</td></tr>';

			echo '<tr><td colspan="2">';show_page_subtitle('income');echo'</td></tr>';
			// +-------------+
			// | national a  |
			// +-------------+
			echo '<tr><td>';
			echo 'National assistance';
			echo '</td><td>';
			$x = get_kw_from_name('national_assistance',$this->getDataString('national_assistance'));
			print remove_number_prefix($x['title']);
			echo '</td></tr>';

			// +-------------+
			// | SECTION 4   |
			// +-------------+
			echo '<tr><td>';
			echo 'Section 4';
			echo '</td><td>';
			$x = get_kw_from_name('section4',$this->getDataString('section4'));
			print remove_number_prefix($x['title']);
			echo '</td></tr>';

			// +--------------+
			// | OTHER INCOME |
			// +--------------+
			echo '<tr><td>';
			echo 'Other Income';
			echo '</td><td>';
			$x = get_kw_from_name('other_income',$this->getDataString('income'));
			print remove_number_prefix($x['title']);
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Income Details';
			echo '</td><td>';
			echo '<textarea name="income_notes" disabled id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('income_notes'))
				. '</textarea>';
			echo '</td></tr>';

			// +---------------+
			// | ACCOMMODATION |
			// +---------------+
			echo '<tr><td colspan="2">';show_page_subtitle('Accommodation');echo'</td></tr>';
			echo '<tr><td>';
			echo 'Current Accommodation';
			echo '</td><td>';
			$x = get_kw_from_name('accomidation',$this->getDataString('accomidation'));
			print remove_number_prefix($x['title']);
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Accommodation Details';
			echo '</td><td>';
			echo '<textarea disabled name="accomidation_notes" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('accomidation_notes'))
				. '</textarea>';
			echo '</td></tr>';

			// +---------------+
			// | FOOD          |
			// +---------------+
			echo '<tr><td colspan="2">';show_page_subtitle('Food');echo'</td></tr>';
			echo '<tr><td>';
			echo 'Sources of Food';
			echo '</td><td>';
			$x = get_kw_from_name('food',$this->getDataString('food'));
			print remove_number_prefix($x['title']);
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Food Details';
			echo '</td><td>';
			echo '<textarea disabled name="food_notes" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('food_notes'))
				. '</textarea>';
			echo '</td></tr>';

			// +----------------+
			// | SOCIAL SUPPORT |
			// +----------------+
			echo '<tr><td colspan="2">';show_page_subtitle('Social Support');echo'</td></tr>';
			echo '<tr><td>';
			echo 'Social Support';
			echo '</td><td>';
			echo '<input type="hidden" name="social" value="yes"/>';
			echo '<input type="checkbox" disabled name="social1" '.($this->getDataInt('social1')==1?"checked":"").' />Family<br/>';
			echo '<input type="checkbox" disabled name="social2" '.($this->getDataInt('social2')==1?"checked":"").' />Friends<br/>';
			echo '<input type="checkbox" disabled name="social3" '.($this->getDataInt('social3')==1?"checked":"").' />Other Community Members<br/>';
			echo '<input type="checkbox" disabled name="social4" '.($this->getDataInt('social4')==1?"checked":"").' />People met through projects<br/>';
			echo '<input type="checkbox" disabled name="social5" '.($this->getDataInt('social5')==1?"checked":"").' />Other People<br/>';
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Social Support Details';
			echo '</td><td>';
			echo '<textarea disabled name="social_notes" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('social_notes'))
				. '</textarea>';
			echo '</td></tr>';

			// +---------------+
			// | HEALTH        |
			// +---------------+
			echo '<tr><td colspan="2">';show_page_subtitle('Health');echo'</td></tr>';
			echo '<tr><td>';
			echo 'Physical Problems';
			echo '</td><td>';
			echo '<textarea disabled name="health_p" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('health_p'))
				. '</textarea>';
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Psychological or<br> Psychiatric Problems';
			echo '</td><td>';
			echo '<textarea disabled name="health_m" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('health_m'))
				. '</textarea>';
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Regular Medications <br> (& aprox start date)';
			echo '</td><td>';
			echo '<textarea disabled name="medications" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('medications'))
				. '</textarea>';
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Admissions to Hospital';
			echo '</td><td>';
			echo '<textarea disabled name="hospital" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('hospital'))
				. '</textarea>';
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Hospital Clinics Attended';
			echo '</td><td>';
			echo '<textarea disabled name="clinics" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('clinics'))
				. '</textarea>';
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'GP\'s details ';
			echo '</td><td>';
			echo '<textarea disabled name="gp" id="input_status_notes" class="frm_tarea" rows="4" cols="60">'
				. clean_output($this->getDataString('gp'))
				. '</textarea>';
			echo '</td></tr>';




			echo '<tr><td colspan="2">';show_page_subtitle('Additional Supporting Infomation');echo'</td></tr>';

			// Notes
			echo "<tr>\n";
			echo "<td>" . f_err_star('client_notes') ."Other Supporting Infomation</td>\n";
			echo '<td><textarea disabled name="client_notes" id="input_client_notes" class="frm_tarea" rows="7" cols="60">'
				. clean_output($this->getDataString('notes'))
				. "</textarea>\n"
				. "</td>\n";
			echo "</tr>\n";
			}
		else
			{
			echo '<input type="hidden" name="name_first" value="'.$this->getDataString('name_first').'"/>';
			echo '<input type="hidden" name="name_last" value="'.$this->getDataString('name_last').'"/>';
			echo '<input type="hidden" name="gender" value="'.$this->getDataString('gender').'"/>';
			echo '<input type="hidden" name="id_client" value="'.$this->getDataInt('id_client').'"/>';
		// CHECKBOXES:
//		echo '<tr>';
//		echo '<td colspan="2" align="center" valign="middle">';
//		show_page_subtitle('Scores and Suitability');	
//		echo '<td>';
//		echo '</tr>';
		echo '<tr><td>';
		echo 'Houseing Suitability:';
		echo '</td><td>';
		echo '<input type=checkbox name="check1" '.($this->getdatastring('check1')=='on' || $this->getDataInt('check1')?"checked":"").'>Private Home<br/>';
		echo '<input type=checkbox name="check2" '.($this->getdatastring('check2')=='on' || $this->getDataInt('check2')?"checked":"").'>Shared House<br/>';
		echo '<input type=checkbox name="check3" '.($this->getdatastring('check3')=='on' || $this->getDataInt('check3')?"checked":"").'>Nightshelter<br/>';
		echo '</td></tr>';
		echo '<tr><td>';
		echo 'Accommodation Priority:</td><td><input type=text name="accom" value="'.$this->getDataString('accom').'"/>';
		echo '<tr><td>';
		echo 'Panel Score:</td><td><input type=text name="pannel" value="'.$this->getDataString('pannel').'"/>';
		echo '</td></tr>';
		//
		// Contacts (e-mail, phones, etc.)
		//
			}
//		if ($mode!='scores')
//			{
//			echo "<tr>\n";
//			echo '<td colspan="2" align="center" valign="middle">';
//			show_page_subtitle(_T('client_subtitle_contacts'));
//			echo '</td>';
//			echo "</tr>\n";
//		
//			show_all_contacts_two('client', $this->getDataInt('id_client'));
//			}
		if ($mode!='salrev')
			echo "</table>\n";
	}
	function printEdit($mode) {
		// Get site preferences
		$client_name_middle = read_meta('client_name_middle');
		$client_citizen_number = read_meta('client_citizen_number');
		$client_civil_status = read_meta('client_civil_status');
		$client_income = read_meta('client_income');
		$meta_date_birth = read_meta('client_date_birth');
		if ($mode!='salrev')
			echo '<table width="99%" border="0" align="center" cellpadding="5" cellspacing="0" class="tbl_usr_dtl">' . "\n";
		
		if ($mode!='scores')
			{
			echo '<tr><td colspan="2">';show_page_subtitle('Personal Infomation');echo'</td></tr>';
			if($this->getDataInt('id_client')) 
				{
				echo "<tr><td>" . _T('client_input_id') . "</td>\n";
				echo "<td>" . $this->getDataInt('id_client')
					. '<input type="hidden" name="id_client" value="' . $this->getDataInt('id_client') . '" /></td></tr>' . "\n";
				}

			// Client name
			echo '<tr><td>' . f_err_star('name_first') . _T('person_input_name_first') . '</td>' . "\n";
			echo '<td><input name="name_first" value="' . clean_output($this->getDataString('name_first')) . '" class="search_form_txt" /></td></tr>' . "\n";
			
			// [ML] always show middle name, if any, no matter the configuration
			if ($this->getDataString('name_middle') || substr($client_name_middle, 0, 3) == 'yes') 
				{
				echo '<tr><td>' . f_err_star('name_middle') . _T('person_input_name_middle') . '</td>' . "\n";
				echo '<td><input name="name_middle" value="' . clean_output($this->getDataString('name_middle')) . '" class="search_form_txt" /></td></tr>' . "\n";
				}
				
			echo '<tr><td>' . f_err_star('name_last') . _T('person_input_name_last') . '</td>' . "\n";
			echo '<td><input name="name_last" value="' . clean_output($this->getDataString('name_last')) . '" class="search_form_txt" /></td></tr>' . "\n";
			
			if (substr($meta_date_birth, 0, 3) == 'yes') 
				{
				echo "<tr>\n";
				echo "<td>" . f_err_star('date_birth') . _Ti('person_input_date_birth') . "</td>\n";
				echo "<td>" 
					. get_date_inputs('date_birth', $this->getDataString('date_birth'), false)
					. "</td>\n";
				echo "</tr>\n";
				}
			
			echo '<tr><td>' . f_err_star('gender') . _T('person_input_gender') . '</td>' . "\n";
			echo '<td><select name="gender" class="sel_frm">' . "\n";

			$opt_sel_male = $opt_sel_female = $opt_sel_unknown = '';
			
			if ($this->getDataString('gender') == 'male')
				$opt_sel_male = 'selected="selected" ';
			else if ($this->getDataString('gender') == 'female')
				$opt_sel_female = 'selected="selected" ';
			else
				$opt_sel_unknown = 'selected="selected" ';
			
			echo '<option ' . $opt_sel_unknown . 'value="unknown">' . _T('info_not_available') . "</option>\n";
			echo '<option ' . $opt_sel_male . 'value="male">' . _T('person_input_gender_male') . "</option>\n";
			echo '<option ' . $opt_sel_female . 'value="female">' . _T('person_input_gender_female') . "</option>\n";
			
			echo "</select>\n";
			echo "</td></tr>\n";


			
//			if ($this->getDataString('id_client')) 
//				{
//				echo "<tr>\n";
//				echo '<td>' . _Ti('time_input_date_creation') . '</td>';
///				echo '<td>' . format_date($this->getDataString('date_creation'), 'full') . '</td>';
//				echo "</tr>\n";
//				}
			
			if (substr($client_citizen_number, 0, 3) == 'yes') 
				{
				echo "<tr>\n";
				echo '<td>' . f_err_star('citizen_number') .  _T('person_input_citizen_number') . '</td>';
				echo '<td><input name="citizen_number" value="' .  clean_output($this->getDataString('citizen_number')) . '" class="search_form_txt" /></td>';
				echo "</tr>\n";
				}
			
			//MATT WAS HERE, ADDING NASS NUMBER TO "INPUT CLIENT"/"EDIT CLIENT" PAGES. THIS AUTOMAGICLY PROPERGATES THANKS TO SELECT * QUERY ABOVE.
			
			echo "<tr>\n";
			echo '<td>' . f_err_star('nass_number') .  _T('person_input_nass_number') . '</td>';
			echo '<td><input name="nass_number" value="' .  clean_output($this->getDataString('nass_number')) . '" class="search_form_txt" /></td>';
			echo "</tr>\n";

//			if (substr($client_civil_status, 0, 3) == 'yes') 
//				{
//				echo "<tr>\n";
//				echo '<td>' . f_err_star('civil_status') . _Ti('person_input_civil_status') . '</td>';
//				echo '<td>';
//				echo '<select name="civil_status">';
//
//				if (! $this->getDataInt('id_client')) 
//					echo '<option value=""></option>';
//
//				$kwg = get_kwg_from_name('civilstatus');
//				$all_kw = get_keywords_in_group_name('civilstatus');
//		
//				// A bit overkill, but if the user made the error of not entering
//				// a valid civil_status, make sure that the field stays empty
//				if (! $this->getDataString('civil_status') || ! count($_SESSION['errors'])) 
//					{
//					if ($this->getDataInt('id_client')) 
//						{
//						$this->data['civil_status'] = $all_kw['unknown']['name'];
//						} 
////					else 
	//					{
	//					$this->data['civil_status'] = $kwg['suggest'];
	//					}
//					}
//		
//				foreach($all_kw as $kw) 
//					{
//					$sel = ($this->getDataString('civil_status') == $kw['name'] ? ' selected="selected"' : '');
//					echo '<option value="' . $kw['name'] . '"' . $sel . '>' . _T($kw['title']) . '</option>';
//					}
//		
//				echo '</select>';
//				echo '</td>';
//				echo "</tr>\n";
//				}
//
//			if (substr($client_income, 0, 3) == 'yes') 
//				{
//				echo "<tr>\n";
//				echo '<td>' . f_err_star('income') .  _Ti('person_input_income') . '</td>';
//				echo '<td>';
//				echo '<select name="income">';
//
//				if (! $this->getDataInt('id_client')) 
//					echo '<option value=""></option>';
//
//				$kwg = get_kwg_from_name('income');
//				$all_kw = get_keywords_in_group_name('income');
//				
//				if (! $this->getDataString('income') && ! count($_SESSION['errors'])) 
//					{
//					if ($this->getDataInt('id_client')) 
//						{
//						$this->data['income'] = $all_kw['unknown']['name'];
//						}
//					else 
//						{
//						$this->data['income'] = $kwg['suggest'];
//						}
//					}
//
//				foreach($all_kw as $kw) 
//					{
//					$sel = ($this->getDataString('income') == $kw['name'] ? ' selected="selected"' : '');
//					echo '<option value="' . $kw['name'] . '"' . $sel . '>' . _T($kw['title']) . '</option>';
//					}
//				
//				echo '</select>';
//				echo '</td>';
//				echo "</tr>\n";
//				}
//		
//			//
//			// Keywords, if any
//			//
//			show_edit_keywords_form('client', $this->getDataInt('id_client'));

			// +-------------+
			// | COUNTRY     |
			// +-------------+
			echo '<tr><td>';
			echo 'Country of Origin';
			echo '</td><td>';
			echo '<select name="country" size="1" class="sel_frm">';
			$x=$this->getDropdown('ethnicity','country');
			echo "</select>\n";
			echo '</td></tr>';
			// +-------------+
			// | LANGUAGE    |
			// +-------------+
			echo '<tr><td>';
			echo 'Language';
			echo '</td><td>';
			echo '<select name="language" size="1" class="sel_frm">';
			$x=$this->getDropdown('language');
			echo "</select>\n";
			echo '</td></tr>';
			// +-------------+
			// | ENG_LEVEL   |
			// +-------------+
			echo '<tr><td>';
			echo 'English Level';
			echo '</td><td>';
			echo '<select name="eng_level" size="1" class="sel_frm">';
			$x=$this->getDropdown('english','eng_level');
			echo "</select>\n";
			echo '</td></tr>';
			// +-------------+
			// | INTERPRETER |
			// +-------------+
			echo '<tr><td>';
			echo 'Interpreter Required';
			echo '</td><td>';
			echo '<select name="intrepreter" size="1" class="sel_frm">';
			$x=$this->getDropdown('interpreter','intrepreter');
			echo "</select>\n";
			echo '</td></tr>';
			// +-------------+
			// | RELIGION    |
			// +-------------+
			echo '<tr><td>';
			echo 'Religion';
			echo '</td><td>';
			echo '<select name="religion" size="1" class="sel_frm">';
			$x=$this->getDropdown('religion');
			echo "</select>\n";
			echo '</td></tr>';
			// +---------------+
			// | DATE OF ENTRY |
			// +---------------+
			echo '<input type="hidden" name="doe" value="yes"/>';
			echo '<tr><td>';
			echo 'Date of Entry';
			echo '</td><td>';
			$the_date=date('Y-m-d');
			echo get_date_inputs('zot',$this->getDataString('entery_date'));
//			echo '<select name="religion" size="1" class="sel_frm">';
//			$x=$this->getDropdown('religion');
//			echo "</select>\n";
			echo '</td></tr>';
			// +-------------+
			// | STATUS      |
			// +-------------+
			echo '<tr><td>';
			echo 'Status';
			echo '</td><td>';
			echo '<select name="status" size="1" class="sel_frm">';
			$x=$this->getDropdown('status');
			echo "</select>\n";
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Status Details';
			echo '</td><td>';
			echo '<textarea name="status_details" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('status_details'))
				. '</textarea>';
			echo '</td></tr>';

			// +-------------+
			// | REFERAL     |
			// +-------------+
			echo '<tr><td>';
			echo 'Referal';
			echo '</td><td>';
			echo '<select name="referal" size="1" class="sel_frm">';
			$x=$this->getDropdown('clientreferal','referal');
			echo "</select>\n";
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Referal Details';
			echo '</td><td>';
			echo '<textarea name="referal_details" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('referal_details'))
				. '</textarea>';
			echo '</td></tr>';

			// +-------------+
			// | CONTACTS    |
			// +-------------+
			echo '<tr><td colspan="2">';show_page_subtitle('Contact Details');echo'</td></tr>';
			echo '<tr><td>Address:</td>' . "\n";
			echo '<td><input name="address" value="' . clean_output($this->getDataString('address')) . '" class="search_form_txt" /></td></tr>' . "\n";
			echo '<tr><td>Telephone:</td>' . "\n";
			echo '<td><input name="telephone" value="' . clean_output($this->getDataString('telephone')) . '" class="search_form_txt" /></td></tr>' . "\n";
			echo '<tr><td>';
			echo 'Other Contacts:';
			echo '</td><td>';
			echo '<textarea name="contact" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('contact'))
				. '</textarea>';
			echo '</td></tr>';
//			// +-------------+
//			// | LETTERS     |
//			// +-------------+
//			echo '<tr><td>';
//			echo 'Documentation';
//			echo '</td><td>';
//			echo '<select name="referal" size="1" class="sel_frm">';
//			$x=$this->getDropdown('letter');
//			echo "</select>\n";
//			echo '</td></tr>';



			echo '<tr><td colspan="2">';show_page_subtitle('Solicitor');echo'</td></tr>';
			// +-------------+
			// | Solicitor   |
			// +-------------+
			echo '<tr><td>';
			echo 'Solicitor\'s details';
			echo '</td><td>';
			echo '<textarea name="solicitor" id="input_status_notes" class="frm_tarea" rows="4" cols="60">'
				. clean_output($this->getDataString('solicitor'))
				. '</textarea>';
			echo '</td></tr>';

			echo '<tr><td colspan="2">';show_page_subtitle('income');echo'</td></tr>';
			// +-------------+
			// | national a  |
			// +-------------+
			echo '<tr><td>';
			echo 'National assistance';
			echo '</td><td>';
			echo '<select name="national_assistance" size="1" class="sel_frm">';
			$x=$this->getdropdown('national_assistance');
			echo "</select>\n";
			echo '</td></tr>';

			// +-------------+
			// | SECTION 4   |
			// +-------------+
			echo '<tr><td>';
			echo 'Section 4';
			echo '</td><td>';
			echo '<select name="section4" size="1" class="sel_frm">';
			$x=$this->getDropdown('section4');
			echo "</select>\n";
			echo '</td></tr>';

			// +--------------+
			// | OTHER INCOME |
			// +--------------+
			echo '<tr><td>';
			echo 'Other Income';
			echo '</td><td>';
			echo '<select name="income" size="1" class="sel_frm">';
			$x=$this->getDropdown('other_income','income');
			echo "</select>\n";
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Income Details';
			echo '</td><td>';
			echo '<textarea name="income_notes" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('income_notes'))
				. '</textarea>';
			echo '</td></tr>';

			// +---------------+
			// | ACCOMMODATION |
			// +---------------+
			echo '<tr><td colspan="2">';show_page_subtitle('Accommodation');echo'</td></tr>';
			echo '<tr><td>';
			echo 'Current Accommodation';
			echo '</td><td>';
			echo '<select name="accomidation" size="1" class="sel_frm">';
			$x=$this->getDropdown('accomidation');
			echo "</select>\n";
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Accommodation Details';
			echo '</td><td>';
			echo '<textarea name="accomidation_notes" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('accomidation_notes'))
				. '</textarea>';
			echo '</td></tr>';

			// +---------------+
			// | FOOD          |
			// +---------------+
			echo '<tr><td colspan="2">';show_page_subtitle('Food');echo'</td></tr>';
			echo '<tr><td>';
			echo 'Sources of Food';
			echo '</td><td>';
			echo '<select name="food" size="1" class="sel_frm">';
			$x=$this->getDropdown('food');
			echo "</select>\n";
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Food Details';
			echo '</td><td>';
			echo '<textarea name="food_notes" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('food_notes'))
				. '</textarea>';
			echo '</td></tr>';

			// +----------------+
			// | SOCIAL SUPPORT |
			// +----------------+
			echo '<tr><td colspan="2">';show_page_subtitle('Social Support');echo'</td></tr>';
			echo '<tr><td>';
			echo 'Social Support';
			echo '</td><td>';
			echo '<input type="hidden" name="social" value="yes"/>';
			echo '<input type="checkbox" name="social1" '.($this->getDataInt('social1')==1?"checked":"").' />Family<br/>';
			echo '<input type="checkbox" name="social2" '.($this->getDataInt('social2')==1?"checked":"").' />Friends<br/>';
			echo '<input type="checkbox" name="social3" '.($this->getDataInt('social3')==1?"checked":"").' />Other Community Members<br/>';
			echo '<input type="checkbox" name="social4" '.($this->getDataInt('social4')==1?"checked":"").' />People met through projects<br/>';
			echo '<input type="checkbox" name="social5" '.($this->getDataInt('social5')==1?"checked":"").' />Other People<br/>';
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Social Support Details';
			echo '</td><td>';
			echo '<textarea name="social_notes" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('social_notes'))
				. '</textarea>';
			echo '</td></tr>';

			// +---------------+
			// | HEALTH        |
			// +---------------+
			echo '<tr><td colspan="2">';show_page_subtitle('Health');echo'</td></tr>';
			echo '<tr><td>';
			echo 'Physical Problems';
			echo '</td><td>';
			echo '<textarea name="health_p" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('health_p'))
				. '</textarea>';
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Psychological or<br> Psychiatric Problems';
			echo '</td><td>';
			echo '<textarea name="health_m" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('health_m'))
				. '</textarea>';
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Regular Medications <br> (& aprox start date)';
			echo '</td><td>';
			echo '<textarea name="medications" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('medications'))
				. '</textarea>';
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Admissions to Hospital';
			echo '</td><td>';
			echo '<textarea name="hospital" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('hospital'))
				. '</textarea>';
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'Hospital Clinics Attended';
			echo '</td><td>';
			echo '<textarea name="clinics" id="input_status_notes" class="frm_tarea" rows="2" cols="60">'
				. clean_output($this->getDataString('clinics'))
				. '</textarea>';
			echo '</td></tr>';

			echo '<tr><td>';
			echo 'GP\'s details ';
			echo '</td><td>';
			echo '<textarea name="gp" id="input_status_notes" class="frm_tarea" rows="4" cols="60">'
				. clean_output($this->getDataString('gp'))
				. '</textarea>';
			echo '</td></tr>';




			echo '<tr><td colspan="2">';show_page_subtitle('Additional Supporting Infomation');echo'</td></tr>';

			// Notes
			echo "<tr>\n";
			echo "<td>" . f_err_star('client_notes') ."Other Supporting Infomation</td>\n";
			echo '<td><textarea name="client_notes" id="input_client_notes" class="frm_tarea" rows="7" cols="60">'
				. clean_output($this->getDataString('notes'))
				. "</textarea>\n"
				. "</td>\n";
			echo "</tr>\n";
			}
		else
			{
			echo '<input type="hidden" name="name_first" value="'.$this->getDataString('name_first').'"/>';
			echo '<input type="hidden" name="name_last" value="'.$this->getDataString('name_last').'"/>';
			echo '<input type="hidden" name="gender" value="'.$this->getDataString('gender').'"/>';
			echo '<input type="hidden" name="id_client" value="'.$this->getDataInt('id_client').'"/>';
			echo '<tr><td>';
			echo 'Houseing Suitability:';
			echo '</td><td>';
			echo '<input type=checkbox name="check1" '.($this->getdatastring('check1')=='on' || $this->getDataInt('check1')?"checked":"").'>Private Home<br/>';
			echo '<input type=checkbox name="check2" '.($this->getdatastring('check2')=='on' || $this->getDataInt('check2')?"checked":"").'>Shared House<br/>';
			echo '<input type=checkbox name="check3" '.($this->getdatastring('check3')=='on' || $this->getDataInt('check3')?"checked":"").'>Nightshelter<br/>';
			echo '</td></tr>';
			echo '<tr><td>';
			echo 'Accommodation Priority:</td><td><input type=text name="accom" value="'.$this->getDataString('accom').'"/>';
			echo '<tr><td>';
			echo 'Panel Score:</td><td><input type=text name="pannel" value="'.$this->getDataString('pannel').'"/>';
			echo '</td></tr>';
			echo '<tr><td>';
			echo _T('fu_input_description');
			echo '</td><td>';
			echo '<textarea ' . $dis . ' name="description" rows="15" cols="60" class="frm_tarea">';
			echo clean_output($this->getDataString('description'));
			echo "</textarea>";
			echo "</td></tr>";
			}
//		if ($mode!='scores')
//			{
//			echo "<tr>\n";
//			echo '<td colspan="2" align="center" valign="middle">';
//			show_page_subtitle(_T('client_subtitle_contacts'));
//			echo '</td>';
//			echo "</tr>\n";		
//			show_edit_contacts_form('client', $this->getDataInt('id_client'));
//			}
		if ($mode!='salrev')
			echo "</table>\n";
	}
}

?>
