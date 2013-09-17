<?php
if ($_SESSION['form_data']['setamount']=='yes')
	{
	$_SESSION['form_data']['description']='Support set at £'.$_SESSION['form_data']['amount'].($_SESSION['form_data']['buspass']?'& bus pass':'').".\n".$_SESSION['form_data']['description'];
	}

/*
	This file is part of the Legal Case Management System (LCM).
	(C) 2004-2006 Free Software Foundation, Inc.

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

	$Id: inc_obj_fu.php,v 1.22 2006/11/22 23:49:07 mlutfy Exp $
*/

// Execute this file only once
if (defined('_INC_OBJ_FU')) return;                                           
define('_INC_OBJ_FU', '1');

include_lcm('inc_db');
include_lcm('inc_obj_generic');
include_lcm('inc_obj_client');
include_once('inc/DataRetrieval.class.php');

class LcmFollowup extends LcmObject {
	var $data; 

	function LcmFollowup($id_fu = 0, $id_case = 0) {
		
		$id_fu = intval($id_fu);
		$id_case = intval($id_case);

		$this->data = array();
		if ($id_fu > 0) { 
			$query = "SELECT fu.*, a.name_first, a.name_middle, a.name_last, " .
						lcm_query_subst_time('fu.date_start', 'fu.date_end') . " as length
					FROM lcm_followup as fu, lcm_author as a
					WHERE id_followup = $id_fu
					  AND fu.id_author = a.id_author";
	
			$result = lcm_query($query);
	
			if (($row = lcm_fetch_array($result))) 
				foreach ($row as $key => $val) 
					$this->data[$key] = $val;
	
		} else {
			if ($id_case > 0) {
				$this->data['id_case'] = $id_case;
			}

			// Set appointment start/end/reminder times to current time
			$this->data['app_start_time'] = date('Y-m-d H:i:s', strtotime('+3 months'));
			$this->data['app_end_time'] = date('Y-m-d H:i:s');
			$this->data['app_reminder'] = date('Y-m-d H:i:s');

			if (isset($_REQUEST['stage']))
				$this->data['new_stage'] = _request('stage');

			if (isset($_REQUEST['type']))
				$this->data['type'] = _request('type');
		}

		// If any, populate form values submitted
		foreach($_REQUEST as $key => $value) {
			$nkey = $key;

			if (substr($key, 0, 3) == 'fu_')
				$nkey = substr($key, 3);

			$this->data[$nkey] = clean_input(_request($key));
		}

		// If any, populate with session variables (for error reporting)
		if (isset($_SESSION['form_data'])) {
			foreach($_SESSION['form_data'] as $key => $value) {
				$nkey = $key;
				if (substr($key, 0, 3) == 'fu_')
					$nkey = substr($key, 3);

				$this->data[$nkey] = clean_input(_session($key));
			}
		}

		// date_start
		if (get_datetime_from_array($_SESSION['form_data'], 'start', 'start', -1, false) != -1)
			$this->data['date_start'] = get_datetime_from_array($_SESSION['form_data'], 'start', 'start', '', false);
	}
	
	/*
		Determines whether the followup is the sort that includes a payment
	*/
	protected function isPayment() {
		return $this->getDataString('type')=='followups43' 
			|| $this->getDataString('bugfix')=='followups43'
			|| $this->getDataString('type')=='followups27';
	}

	/*
		Determines whether the followup is a welfare payment in particular
	*/
	protected function isWelfarePayment() {
		return $this->getDataString('type')=='followups27';
	}

		
	/*
		Display description in editable box
	*/
	protected function editDescription()
	{
		echo '<textarea ' . $dis . ' name="description" rows="15" cols="60" class="frm_tarea">';
		echo clean_output($this->getDataString('description'));
		echo "</textarea>";
	}
	
	/*
		Display amount in editable box
	*/
	protected function editAmount()
	{
		echo '<tr><td>';
		echo 'Paid:';
		echo '</td><td>';
		echo '<select name="outcome_amount">';
		for ($i=0;$i<=60;$i=$i+5)
		{
			$sel='';
			if ($this->getDataInt('outcome_amount')==$i) {
				$sel='selected';
			}
			echo '<option value="'.$i.'" '.$sel.'>'.$i.'</option>';
		}
		echo '</select>';
		echo '</td></tr>';
	}

	/*
		Display whether bus pass given, in editable checkbox
	*/
	protected function editBusPass()
	{
		echo '<tr><td>';
		echo 'Bus Pass Given:';
		echo '</td><td>';
		echo '<input type="checkbox" name="bus_pass_given"';
		if ($this->data['bus_pass_given'] === '1') {
			echo ' checked';
		}
		echo ' />';
		echo '</td></tr>';

	}
	function validate() {
		$errors = array();
		if ($this->getDataInt('user') == 1000000)
			{
			$errors['user'] = "Please select a user";
			}

		// * Check for id_case
		if (! ($this->getDataInt('id_case') > 0))
			$errors['id_case'] = "Internal error: No id_case found";

		// * Check start date

		$unix_date_start = strtotime($this->getDataString('date_start'));

		if (($unix_date_start < 0) || ! checkdate_sql($this->getDataString('date_start')))
			$errors['date_start'] = _Ti('time_input_date_start') . 'Invalid start date.'; // TRAD

		if (! is_numeric($this->getDataFloat('sumbilled', 0.0)))
			$errors['sumbilled'] = _Ti('fu_input_sum_billed') . 'Incorrect format, must be 00000.00'; // TRAD

		// * Check end date
		// [ML] This is probably very buggy, because I re-wrote parts of it
		// to make it LCM 0.7.0 compliant, but it's a hell of a mess!
		// And parts of this code should be in the constructor.
		global $prefs;
		if ($prefs['time_intervals'] == 'absolute') {
			if (isempty_datetime_from_array($_SESSION['form_data'], 'end', 'date_only')) {
				// Set to default empty date if all fields empty
				$this->data['date_end'] = '0000-00-00 00:00:00';
			} elseif (! isset_datetime_from_array($_SESSION['form_data'], 'end', 'date_only')) {
				// Report error if some of the fields empty
				$this->data['date_end'] = get_datetime_from_array($_SESSION['form_data'], 'end', 'start', '', false);
				$errors['date_end'] = 'Partial end date!'; // TRAD
			} else {
				$this->data['date_end'] = get_datetime_from_array($_SESSION['form_data'], 'end', 'start', '', false);
				$unix_date_end = strtotime($this->getDataString('date_end'));

				if ( ($unix_date_end<0) || !checkdate_sql($this->getDataString('date_end')))
					$errors['date_end'] = 'Invalid end date.'; // TRAD
			}
		} else {
			$valid_interval = true;
			$unix_date_end = $unix_date_start;

			$_SESSION['form_data']['delta_days'] = trim($_SESSION['form_data']['delta_days']);
			$_SESSION['form_data']['delta_hours'] = trim($_SESSION['form_data']['delta_hours']);
			$_SESSION['form_data']['delta_minutes'] = trim($_SESSION['form_data']['delta_minutes']);

			if (is_numeric(_session('delta_days', 0)) && _session('delta_days', 0) >= 0)
				$unix_date_end += (_session('delta_days', 0)) * 86400;
			else
				$valid_interval = false;

			if (is_numeric(_session('delta_hours', 0)) && _session('delta_hours', 0) >= 0)
				$unix_date_end += (_session('delta_hours', 0)) * 3600;
			else
				$valid_interval = false;

			if (is_numeric(_session('delta_minutes', 0)) && _session('delta_minutes', 0) >= 0)
				$unix_date_end += (_session('delta_minutes', 0)) * 60;
			else
				$valid_interval = false;


			if ($valid_interval) {
				$this->data['date_end'] = date('Y-m-d H:i:s', $unix_date_end);
			} else {
				$errors['date_end'] = _Ti('time_input_length') . 'Invalid time interval.'; // TRAD
				$this->data['date_end'] = $_SESSION['form_data']['date_start'];
			}
		}

		// Description
		/* [ML] This was requested to be optional (MG, PDO)
		   if ( !(strlen($this->data['description']) > 0) )
		   $errors['description'] = _Ti('fu_input_description') . _T('warning_field_mandatory');
		 */

		validate_update_keywords_request('followup', $this->getDataInt('id_followup'));

		if ($_SESSION['errors'])
			$errors = array_merge($errors, $_SESSION['errors']);

		//
		// Custom validation functions
		//
		$id_case = $this->getDataInt('id_case');
		$fields = array('description' => 'FollowupDescription');

		foreach ($fields as $f => $func) {
			if (include_validator_exists($f)) {
				include_validator($f);
				$class = "LcmCustomValidate$func";
				$data = $this->getDataString($f);
				$v = new $class();

				if ($err = $v->validate($id_case, $data)) 
					$errors[$f] = _Ti('fu_input_' . $f) . $err;
			}
		}

		return $errors;
	}

	function save($weird = 0) {
		$_SESSION['matt_data']['user']=$this->getDataString('user');
	
		$errors = $this->validate();
		if (count($errors))
			return $errors;

		if ($this->getDataString('setamount')=='yes')
			{
			$this->data['description']='Support set at £'.$this->getDataString('amount').($this->getDataString('buspass')?'& bus pass':'').".\n".$this->getDataString('description');
			}
		
		// Update
		$fl = " date_start = '" . substr($this->getDataString('date_start'),0,17) . "00',
				date_end   = '" . $this->getDataString('date_end') . "',
				type       = '" . $this->getDataString('type') . "',
				sumbilled  = " . $this->getDataFloat('sumbilled', 0.00);
		


		if (!$weird)
			{
			$fl .= ", description  = '" . $this->getDataString('description') . "'";
			}
			$fl .= ", outcome_amount = '". $this->getDataString('outcome_amount') . "'";


		if ($this->getDataInt('id_followup') > 0) {
			// Edit of existing follow-up
			$id_followup = $this->getDataInt('id_followup');
		
			if (!allowed($this->getDataInt('id_case'), 'e')) 
				lcm_panic("You don't have permission to modify this case's information. (" . $this->getDataInt('id_case') . ")");

			if (allowed($this->getDataInt('id_case'), 'a')
					&& (! (is_status_change($this->getDataString('type'))
							|| $this->getDataString('type') == 'assignment'
							|| $this->getDataString('type') == 'unassignment')))
			{
				if ($this->getDataString('delete'))
					$fl .= ", hidden = 'Y'";
				else
					$fl .= ", hidden = 'N'";
			} else {
				$fl .= ", hidden = 'N'";
			}
		
			$fl .= ", bus_pass_given = " . $this->getDataInt('bus_pass_given');
			$q = "UPDATE lcm_followup SET $fl WHERE id_followup = $id_followup";
			$result = lcm_query($q);

			lcm_query($q);
		} else {
			// New follow-up
			if (!allowed($this->getDataInt('id_case'), 'w'))
				lcm_panic("You don't have permission to add information to this case. (" . $this->getDataInt('id_case') . ")");
			// Get the current case stage
			$q = "SELECT status FROM lcm_case WHERE id_case=" . $this->getDataInt('id_case', '__ASSERT__');
			$result = lcm_query($q);

			if ($row = lcm_fetch_array($result)) {
				$case_status = lcm_assert_value($row['status']);
			} else {
				lcm_panic("There is no such case (" . $this->getDataInt('id_case') . ")");
			}

			// Add the new follow-up
			$q = "INSERT INTO lcm_followup
					SET id_case=" . $this->getDataInt('id_case') . ", bus_pass_given = ". $this->getDataInt('bus_pass_given').
					", id_author=" . ($this->getDataInt('user')>0?$this->getDataInt('user'):$GLOBALS['author_session']['id_author']) . ",
					$fl";

			
			lcm_query($q);
			$this->data['id_followup'] = lcm_insert_id('lcm_followup', 'id_followup');
			
			$fh = fopen('./log/logs','a') or die ('SAD FACE');
			fwrite($fh, $q."\n---------------------------------\n");

			// Set relation to the parent appointment, if any
			if ($this->getDataInt('id_app')) 
				{
				$q = "INSERT INTO lcm_app_fu 
						SET id_app=" . $this->getDataInt('id_app') . ",
							id_followup=" . $this->getDataInt('id_followup', '__ASSERT__') . ",
							relation='child'";
				$result = lcm_query($q);
				}

			// Update case status
			$status = '';
			$stage = '';
			//MATT WAS HERE. STOP AUTOMATICLY 'OPENING' 'DRAFT' CASES WHEN FIRST FU GOES ON.
			if ($case_status == 'closed')
				{
				$status='open';
				}
			else
				{
				switch ($this->getDataString('type')) 
					{
					case 'conclusion' :
						$status = 'closed';
						break;
					case 'suspension' :
						$status = 'suspended';
						break;
					case 'opening' :
					case 'resumption' :
					case 'reopening' :
						$status = 'open';
						break;
					case 'merge' :
						$status = 'merged';
						break;
					case 'deletion':
						$status = 'deleted';
						break;
					case 'followups20' : // APPLICATION SUCESSFUL, SUPPRT GRANTED
						$stage = 'supported';
						break;
					case 'followups19' : // APPLICATION REJECTED
						$stage = 'rejected';
						$status = 'closed';
						break;
					case 'followups18' : // add to waiting list
						$stage = 'waiting list';		
						break;
					case 'followups22' : // TERMINATED
						$stage = 'terminated';
						$status= 'closed';
						break;
					case 'followups41' : // ACCOMIDATION RESERVED FOR CURRENTLY HOUSED CLIENT
						$stage = 'accomreserved';
						break;
					case 'followups23' : // ACCOMIDATION RESERVED
						$stage = 'reserved';
						break;
					case 'followups24' : // ACCOMIDATION MOVED IN
						$stage = 'accom';
						break;
					case 'followups42' : // UNRESERVE ACCOMIDATION FOR ALLREADY ACCOMIDATED CLIENT
						$stage = 'accom';
						break;
					case 'followups26' : // ACCOMIDATION UNRESERVED
						$stage = 'waiting list';
						break;
					case 'followups31' : // TEMP SUPPORT
						$stage = 'submitted supported';
						break;
					case 'followups32' : // TEMP SUPPORT OVER
						$stage = 'submitted';
						break;
					case 'followups35' : // BEFRIENDED
						$stage = 'bef';
						break;
					case 'followups40': //defered
						$stage = $this->getDataString('stage');
						break;
					}
				}

			if ($stage)
				{
				$q = "UPDATE lcm_case SET stage=".$stage." WHERE id_case=".$this->getDataInt('id_case');
				}

			if ($status || $stage) {
				$q = "UPDATE lcm_case
						SET " . ($status ? "status='$status'" : '') . ($status && $stage ? ',' : '') . ($stage ? "stage='$stage'" : '') . "
						WHERE id_case=" . $this->getDataInt('id_case');

				lcm_query($q);
					}
		}

		// Keywords
		update_keywords_request('followup', $this->getDataInt('id_followup'));

		return $errors;
	}
}

class LcmFollowupInfoUI extends LcmFollowup {
	var $show_conclusion;
	var $show_sum_billed;

	function LcmFollowupInfoUI($id_fu = 0) {
		$this->LcmFollowup($id_fu);
		// In rintEdit(), whether to show "conclusion" fields
		$this->show_conclusion = false;
		// In rintEdit(), whether to check for sumbilled
		$this->show_sum_billed = read_meta('fu_sum_billed');
	}

	function printGeneral($show_subtitle = true, $allow_edit = true) {
		if ($show_subtitle)
			show_page_subtitle(_T('generic_subtitle_general'), 'cases_intro');
		echo '<ul class="info">';
		// TODO: fix html
		
		// Author
		echo '<li>'
			. '<span class="label2">' . _Ti('case_input_author') . '</span>'
			. '<span class="value2">' . get_author_link($this->data) . '</span>'
			. "</li>\n";
		
		// Date start
		echo '<li>'
			. '<span class="label2">' . _Ti('time_input_date_when') . '</span>'
			. '<span class="value2">' . format_date($this->data['date_start']) . '</span>'
			. "</li>\n";
		
	
		// FU type
		echo '<li>'
			. '<span class="label2">' . _Ti('fu_input_type') . '</span>'
			. '<span class="value2">' . _Tkw('followups', $this->data['type']) . '</span>'
			. "</li>\n";

		// Keywords
		show_all_keywords('followup', $this->getDataInt('id_followup'));

		if ($this->data['outcome'])
			{
			$zot = get_kw_from_id($this->data['outcome']);
			echo '<li>'
				. '<span class="label2">' . 'Outcome: ' . '</span>'
				. '<span class="value2"><b>' . $zot['title'] . '</b></span>'
				. "</li>\n";
			}
		if ($this->data['outcome_amount'])
			{
			echo '<li>'
				. '<span class="label2">' . 'Outcome Amount <b>£</b>: '. '</span>'
				. '<span class="value2"><b>' . $this->data['outcome_amount'] . '</b></span>'
				. "</li>\n";
			}

		
		
		// Description//MATT READ HERE
		$desc = get_fu_description($this->data, false);
		
		echo '<li class="large">'
			. '<span class="label2">' . _T('fu_input_description').' ' . '</span>'
			. '<span class="value2">' . $desc . '</span>'
			. "</li>\n";
		
		// Sum billed (if activated from policy)
		if ($this->show_sum_billed == 'yes') {
			echo '<li>'
				. '<span class="label2">' . _T('fu_input_sum_billed') . '</span>'
				. '<span class="value2">';

			echo  format_money(clean_output($this->data['sumbilled']));
			$currency = read_meta('currency');
			echo htmlspecialchars($currency);

			echo '</span>';
			echo "</li>\n";
		}
						
		echo "</ul>\n";
	}

	// XXX error checking! ($_SESSION['errors'])
	function printEdit($special='') {
		$oneoff=false;
		if ($this->getDataInt('id_case'))
			{
			$admin = allowed($this->getDataInt('id_case'), 'a'); // FIXME
			$edit  = allowed($this->getDataInt('id_case'), 'e'); // FIXME
			$write = allowed($this->getDataInt('id_case'), 'w'); // FIXME (put in constructor)
			}
		else
			{
			$admin = 1;
			$edit = 1;
			$write = 1;
			}
		if (!($this->data['id_followup']))
			{
			$zot = $write;
			}
		else	
			{
			$zot = $edit;
			}
		// FIXME: not sure whether this works as previously

		// +---------------------------------------------------+
		// | ADD GREEN HEADER: GROUP-USERNAME AND DATE OF WORK |
		// +---------------------------------------------------+
		if (isset($this->data['id_case']) && $this->data['id_case']) { 
			echo DataRetrieval::getClientNameByCaseId($this->data['id_case']);
		}
		echo '<table class="tbl_data_box">';
		$dis = isDisabled(! ($admin || $zot));
		
		echo '<tr><td>';
		echo f_err_star('date_start') . '<img src="images/office-calendar.png"/> <b>On: </b>'; 
		echo '</td><td>';
		$name = (($admin || $zot) ? 'start' : '');
		$date = date('Y-m-d H:i:s');
		$year = (int) substr($date,0,4);
		$month = (int) substr($date,5,2);
		$day = (int) substr($date,8,2);
		$hour = (int) substr($date,11,2);
		$min = (int) substr($date,14,2);
		echo get_date_inputs($name, date('Y-m-d'), false);
		echo ' ' . _T('time_input_time_at') . ' ';
		echo get_time_inputs($name, null, $hours24 = false, null, null, true);
		echo '<a href="#" onClick="
			document.forms[\'form\'][\'start_year\'].value = '.$year.';
			document.forms[\'form\'][\'start_month\'].value = '.$month.';
			document.forms[\'form\'][\'start_day\'].value = '.$day.';
			document.forms[\'form\'][\'start_hour\'].value = '.$hour.';
			document.forms[\'form\'][\'start_minutes\'].value = '.$min.';
			" class="run_lnk">Today</a>';
		echo '</td></tr>';
//echo '<form name="editfu" action="upd_fu.php" method="post" onSubmit="var but = document.forms[\'editfu\'][\'submit\']; but.disabled=true; but.innerHTML=\'Please wait\'; return true;">';
		if ($GLOBALS['author_session']['status'] == 'group')
			{
			echo '<tr><td>';
			echo f_err_star('user') . '<img src="images/system-users.png"/> <b>By:</b>';
			echo '</td><td>';
			$q = 'select * from lcm_author where status="normal" and id_office="'.$GLOBALS['author_session']['id_author'].'"';
			$result = lcm_query ($q);
			$def='';
			echo '<select name="user" size="1" class="sel_frm">';
			if ($_SESSION['form_data']['user'])
				{
				$id_user= $_SESSION['form_data']['user'];
				}
			elseif ($_SESSION['matt_data']['user'])
				{
				$id_user= $_SESSION['matt_data']['user'];
				}
			else
				{
				echo '<option value="1000000">Please Select</option>'; 
				}
			while ($row = lcm_fetch_array($result))
				{
				if ($row['id_author']==$id_user)
					$def='selected';
				else
					$def='';
				echo '<option value="'.$row['id_author'].'" '.$def.'>'.$row['name_first'].' '.$row['name_last'].'</option>';
				}
			echo '</select>';
			echo '</td></tr>';
			}
		echo '</table>';
		no_tabs();
		echo '<table class="tbl_usr_dtl" width="100%">' . "\n";


		// +-----------------------------------------------------------+
		// | CHOOSE FOLLOWUP BASED AUTOMATICALLY ON STAGE (IF PROVIED) |
		// +-----------------------------------------------------------+
		
		if (_request('submit') == 'set_stage' || $this->getDataString('type') == 'stage_change' || $this->getDataString('new_stage')!='') 
			{
			if (_request('ctype'))
				{
				echo '<input type="hidden" name="ctype" value="'._request('ctype').'">';
				}
			if (_request('stage') == 'submitted supported')
				{
				echo '<input type="hidden" name="type" value="followups31">';
				echo '<input type="hidden" name="stage" value="submitted supported">';
				}
			elseif (_request('stage') == 'submitted unsupported')
				{
				echo '<input type="hidden" name="type" value="followups32">';
				echo '<input type="hidden" name="stage" value="submitted">';
				}
			elseif (_request('stage') == 'submitted2')
				{
				echo '<input type="hidden" name="type" value="followups40">';
				echo '<input type="hidden" name="stage" value="submitted2">';
				}
			elseif (_request('stage') == 'submitted3')
				{
				echo '<input type="hidden" name="type" value="followups40">';
				echo '<input type="hidden" name="stage" value="submitted3">';
				}
			elseif (_request('stage') == 'submitted4')
				{
				echo '<input type="hidden" name="type" value="followups40">';
				echo '<input type="hidden" name="stage" value="submitted4">';
				}
			elseif (_request('stage') == 'waiting list')
				{
				echo '<input type="hidden" name="type" value="followups18">';
				echo '<input type="hidden" name="stage" value="waiting list">';
				}
			elseif (_request('stage') == 'terminated')
				{
				echo '<input type="hidden" name="type" value="followups22">';
				echo '<input type="hidden" name="stage" value="terminated">';
				}
			elseif (_request('stage') == 'rejected')
				{
				echo '<input type="hidden" name="type" value="followups19">';
				echo '<input type="hidden" name="stage" value="rejected">';
				}
			elseif (_request('stage') == 'accom')
				{
				echo '<input type="hidden" name="type" value="followups24">'; //moved in
				echo '<input type="hidden" name="stage" value="accom">';
				}
			elseif (_request('stage') == 'supported')
				{
				echo '<input type="hidden" name="type" value="followups20">';
				echo '<input type="hidden" name="stage" value="supported">';
				}
			elseif (_request('stage') == 'reserved')
				{
				echo '<input type="hidden" name="type" value="followups23">';
				echo '<input type="hidden" name="stage" value="reserved">';
				}
			elseif (_request('stage') == 'accomreserved')
				{
				echo '<input type="hidden" name="type" value="followups41">';
				echo '<input type="hidden" name="stage" value="accomreserved">';
				}
			elseif (_request('stage') == 'unreserved')
				{
				echo '<input type="hidden" name="type" value="followups26">';
				echo '<input type="hidden" name="stage" value="waiting list">';
				}
			elseif (_request('stage') == 'unreserved2')
				{
				echo '<input type="hidden" name="type" value="followups42">'; //movement to new room canceled.
				echo '<input type="hidden" name="stage" value="accom">';
				}
			elseif (_request('stage') == 'bef')
				{
				echo '<input type="hidden" name="type" value="followups35">';
				echo '<input type="hidden" name="stage" value="bef">';
				}
			}
		elseif ($this->getDataString('type')=='opening' || $this->getDataString('bugfix')=='opening')
			{
			echo '<input type="hidden" name="bugfix" value="review">';
			echo '<input type="hidden" name="type" value="opening">';
			}
		elseif ($this->getDataString('type')=='salreview' || $this->getDataString('bugfix')=='salreview')
			{
			echo '<input type="hidden" name="type" value="followups39">';
			echo '<input type="hidden" name="bugfix" value="salreview">';
			}
		elseif ($this->getDataString('type')=='review' || $this->getDataString('bugfix')=='review')
			{
			echo '<input type="hidden" name="type" value="followups21">';
			echo '<input type="hidden" name="bugfix" value="review">';
			}
		elseif ($this->getDataString('type')=='followups27' || $this->getDataString('bugfix')=='followups27')
			{
			echo '<input type="hidden" name="type" value="followups27">';
			echo '<input type="hidden" name="bugfix" value="followups27">';
			}
		elseif ($this->getDataString('type')=='followups28' || $this->getDataString('bugfix')=='followups28')
			{
			echo '<input type="hidden" name="type" value="followups28">';
			echo '<input type="hidden" name="bugfix" value="followups28">';
			}
		elseif ($this->getDataString('type')=='followups30' || $this->getDataString('bugfix')=='followup30')
			{
			echo '<input type="hidden" name="type" value="followups30">';
			echo '<input type="hidden" name="bugfix" value="followups30">';
			}
		elseif ($this->getDataString('type')=='followups34' || $this->getDataString('bugfix')=='followup34')
			{
			echo '<input type="hidden" name="type" value="followups34">';
			echo '<input type="hidden" name="bugfix" value="followups34">';
			}
		elseif ($this->getDataString('type')=='followups43' || $this->getDataString('bugfix')=='followup43')
			{//emergency payment
			echo '<input type="hidden" name="type" value="followups43">';
			echo '<input type="hidden" name="bugfix" value="followups43">';
			}
		elseif ($this->getDataString('type')=='followups44' || $this->getDataString('bugfix')=='followup44')
			{//emergency payment
			echo '<input type="hidden" name="type" value="followups44">';
			echo '<input type="hidden" name="bugfix" value="followups44">';
			}
		else 
			{
			if ($special=='opening')
				{
				echo "<input type='hidden' name='type' value='followups17'>";
				}
			else
				{

				
				echo "<tr>\n";
				echo "<td>" . _T('fu_input_type') . "</td>\n";
				echo "<td>";
				echo '<select ' . $dis . ' name="type" size="1" class="sel_frm">' . "\n";

				$default_fu = get_suggest_in_group_name('followups');
				$futype_kws = get_keywords_in_group_name('followups');
				$kw_found = false;
				$matt = $this->getDataString('type');
				foreach($futype_kws as $kw) 
					{
					$sel = isSelected($kw['name'] == $default_fu);
					if ($sel) $kw_found = true;
					$sel = isSelected($matt == $kw['name']);
					if ($sel) $kw_found = true;
					echo '<option value="' . $kw['name'] . '"' . $sel . '>' . _T(remove_number_prefix($kw['title'])) . "</option>\n";
					}
				// Exotic case where the FU keyword was hidden by the administrator,
				// but an old follow-up using that keyword is being edited.
				if (! $kw_found)
					echo '<option selected="selected" value="' . $default_fu . '">' . _Tkw('followups', $default_fu) . "</option>\n";
	
				echo "</select>\n";
				echo "</td>\n";
				echo "</tr>\n";
				}
			}

		// Keywords (if any)
		show_edit_keywords_form('followup', $this->getDataInt('id_followup'));
		
		//+------------------------------------+
		//| FOR PAYMENTS in general, SHOW OUTCOME AMOUNT
		//+------------------------------------+
		if ($this->isPayment()) {
			$this->editAmount();	
		}
	
		/*
			For welfare payments, show bus pass checkbox
		*/
		if ($this->isWelfarePayment()) {
			$this->editBusPass();
		}
		
		//+----------------------------------------+
		//| SHOW POSSIBLE POST-IT NOTE RECEPITANTS |
		//+----------------------------------------+
		if ($this->getDataString('type')=='followups34' || $this->getDataString('bugfix')=='followups34')
			{
			echo "<tr><td>";
			echo "Recipient:";
			echo "</td><td>";
			echo "<select name='cc'>";
			echo "<option value='post-admin'>Admin Team</option>";
			echo "<option value='post-accom'>Accomidation Team</option>";
			echo "<option value='post-panel'>Panel</option>";
			echo "<option value='post-sal'>SAL</option>";
			echo "<option value='post-helpd'>Help Desk</option>";
			echo "<option value='post-bef'>Befrienders</option>";
			echo "<option value='post-tres'>Tresuary</option>";
			echo "<option value='post-ns'>Night Shelter</option>";
			echo "</select>";
			echo "</td></tr>";
			echo "<tr><td>";
			echo "Colour:";
			echo "</td><td>";
			echo "<select name='colour'>";
			echo "<option value='yellow'>Yellow</option>";
			echo "<option value='blue'>Blue</option>";
			echo "<option value='green'>Green</option>";
			echo "<option value='pink'>Pink</option>";
			echo "</select>";
			echo "</td></tr>";
			}




		//+--------------------------------------------------------------------------+
		//| SHOW CLIENTS WANTING A BEFRIENDER IF  NECESSARY (EG THERE IS NO id_case) |
		//+--------------------------------------------------------------------------+

		if ((($this->getDataString('ctype') == 'befriender') && ($this->getDataString('stage') == 'bef' ))&&(!$this->getDataString('id_case')))
			{
			echo "<tr><td>";
			echo 'Select Client:';
			echo '</td><td>';
			$q = "SELECT cl.*, c.id_case
					from lcm_client as cl 
					left join lcm_case_client_org as cco on cl.id_client = cco.id_client 
					left join lcm_case as c on c.id_case = cco.id_case 
					where c.type_case = 'befriender' and c.stage='submitted'";
			$result = lcm_query($q);
			$checked = true;
			while ($row = lcm_fetch_array($result))
				{
				echo "<input type='radio' ".($checked?'checked':'')." name='eyedee_case' value='".$row['id_case']."'>
						<a class='content_link' href='client_det.php?client=".$row['id_client']."'>".get_person_name($row)."</a>
						</radio>
						<br />";
				if ($checked) $checked=false;
				}
				if ($checked)
					{
					echo 'There are no clients waiting for a befriender. Sorry';
					}
			echo "</td></tr>";
			}
		//+---------------------------------------------------------------+
		//| SHOW CLIENTS IN HOUSING IF NECESSARY (EG THERE IS NO id_case) |
		//+---------------------------------------------------------------+

		if ((($this->getDataString('ctype') == 'accomidation') && ($this->getDataString('stage') == 'accomreserved'))&&(!$this->getDataString('id_case')))
			{
			echo "<tr><td>";
			echo 'Select Client:<br /><small>(From Accommodated List)</small>';
			echo '</td><td>';
			$q="SELECT cl.*, c.id_case, r.name as roomname from lcm_client as cl left join lcm_case_client_org as cco on cco.id_client = cl.id_client left join lcm_case as c on c.id_case = cco.id_case left join lcm_placement as p on c.id_case = p.id_case left join lcm_room as r on r.id_room = p.id_room where c.status='open' and c.stage='accom' and p.status='active'";
//					from lcm_client as cl 
//					left join lcm_case_client_org as cco on cl.id_client = cco.id_client 
//					left join lcm_case as c on c.id_case = cco.id_case 
//					where c.type_case = 'accomidation' and c.stage='waiting list'";
			$result = lcm_query($q);
			$checked = true;

	//		include_lcm('inc_obj_case');
	//		$listtype='12';
	//		$case_list = new LcmCaseListUI();
	//		$case_list->start($listtype);
	//		$case_list->printList($listtype);
	//		$case_list->finish();
			echo '<table>';
			while ($row = lcm_fetch_array($result))
				{
				echo "<tr><td><input type='radio' ".($checked?'checked':'')." name='eyedee_case' value='".$row['id_case']."'>
						<a class='content_link' href='client_det.php?client=".$row['id_client']."'>".get_person_name($row)."</a>
						</radio></td>
						<td>(Living in ".$row['roomname'].")</td>
						</tr>";
				if ($checked) $checked=false;
				}
			echo '</table>';
			if ($checked)
				{
				echo 'There are no clients in accommodation. Sorry';
				}
			echo "</td></tr>";
			}
		//+--------------------------------------------------------------------+
		//| SHOW CLIENTS ON WAITING LIST IF NECESSARY (EG THERE IS NO id_case) |
		//+--------------------------------------------------------------------+

		if ((($this->getDataString('ctype') == 'accomidation') && ($this->getDataString('stage') == 'accom' || $this->getDataString('stage')=='reserved'))&&(!$this->getDataString('id_case')))
			{
			echo "<tr><td>";
			echo 'Select Client:<br />(From Waiting List)';
			echo '</td><td>';
			$q="SELECT cco.id_client, ap.title as ttl, max(fu.date_start) as date_start, fu.description, c.amount, c.id_case, cl.name_first, cl.name_last, cl.check1, cl.check2, cl.check3, cl.pannel, cl.accom, kw.title, c.status, c.public, c.pub_write, c.date_creation, c.date_update, c.type_case, c.stage FROM lcm_case as c NATURAL JOIN lcm_case_author as a NATURAL LEFT JOIN lcm_keyword_case as kc LEFT JOIN lcm_keyword as kw ON kc.id_keyword = kw.id_keyword LEFT JOIN lcm_case_client_org as cco ON c.id_case = cco.id_case LEFT JOIN lcm_client as cl ON cco.id_client = cl.id_client LEFT JOIN ( select a.* from lcm_app as a where a.dismissed = false ) as ap on ap.id_case = c.id_case LEFT JOIN lcm_followup as fu on fu.id_case = c.id_case WHERE (fu.type='followups18') AND c.type_case = \"Accomidation\" AND c.stage = \"Waiting List\" AND c.status = \"Open\" group by cco.id_case";
			$result = lcm_query($q);
			$checked = true;

			echo '<table>';
			while ($row = lcm_fetch_array($result))
				{
				echo "<tr><td><input type='radio' ".($checked?'checked':'')." name='eyedee_case' value='".$row['id_case']."'>
						<a class='content_link' href='client_det.php?client=".$row['id_client']."'>".get_person_name($row)."</a>
						</radio></td>
						<td>(Priority: ".($row['accom']==1?"<b>":"").$row['accom'].($row['accom']==1?"</b>":"").", On list since: ".format_date($row['date_update'],'date_short').")</td>
						</tr>";
				if ($checked) $checked=false;
				}
			echo '</table>';
			if ($checked)
				{
				echo 'There are no clients on the accomidation waiting list. Sorry';
				}
			echo "</td></tr>";
			}
		//+------------------------------------+
		//| SHOW LENGTH OF SUPPORT FOR REVIEWS |
		//+------------------------------------+
		if ($this->getDataString('type')=='review')
			{
			$zap = lcm_fetch_array(lcm_query('select * from lcm_followup where id_case = '.$this->getDataInt('case') .' and ((type = \'followups24\')or(type=\'followups20\' ))'));
			$zot = getdate();
			$days = round(($zot[0] - strtotime(($zap['date_start'])))/86400);
			echo "<tr><td colspan=2>";
			echo "<b>";
			echo "";
			echo ($days<274?'Note':'Warning').": This client has been ".($this->getDataString('ctype')=='accomidation'?'accomidated':'supported')." since ". format_date($zap['date_start'],'date_short').' ('.$days.' days.)';



			echo "</b>" ;
			}

		//+-------------------------------------------------+
		//| SHOW CLIENT DETAILS UPDATE PAGE FOR SAL REVIEWS |
		//+-------------------------------------------------+
		if ($this->getDataString('type')=='salreview'||$this->getDataString('bugfix')=='salreview')
			{
			$obj_client = new LcmClientInfoUI($this->getDataInt('client'));
			$obj_client->printEdit('salrev');
			echo '<tr><td colspan="2">';show_page_subtitle('Action Points');echo'</td></tr>';
			}


		//+----------------------------------------------------------+
		//| SHOW DESCRIPTION BOX FOR MOST TYPES OF FU (NOT OPENINGS) |
		//+----------------------------------------------------------+
		if (
			(!($this->getDataString('type')=='opening' || $this->getDataString('bugfix')=='opening'))
			&&
			(!($this->getDataString('type')=='room_provisional' || $this->getDataString('bugfix')=='room_provisional'))
		   )
			{
			echo "<tr>\n";
			echo '<td width="15%" valign="top">' . f_err_star('description') . ($this->getDataString('bugfix')=='salreview'||$this->getDataString('type')=='salreview'?'Recommendations:':_T('fu_input_description')) . "</td>\n";
			echo '<td>';


			if ($this->getDataString('type') == 'assignment' || $this->getDataString('type') == 'unassignment') 
				{
				// Do not allow edit of assignment
				echo '<input type="hidden" name="description" value="' . $this->getDataString('description') . '" />' . "\n";
				echo get_fu_description($this->data);
				} 
			else 
			{
				$this->editDescription();
			}
			echo "</td></tr>\n";
			}	
	
		// +------------------------------------+
		// | EDIT WEEKLY SUPPORT, IF APPLICABLE |
		// +------------------------------------+
		if 	($this->getDataString('type')=='review' ||
				$this ->getDataString('bugfix')=='review' ||
				(($this->getDataString('type')=='stage_change'||$this->getDataString('bugfix')=='stage_change') &&
				 (
				 $this->getDataString('stage')=='supported' ||
				 $this->getDataString('stage')=='accom' ||
				 $this->getDataString('stage')=='terminated' ||
				 $this->getDataString('stage')=='rejected' ||
				 $this->getDataString('stage')=='submitted supported'||
				 $this->getDataString('stage')=='submitted unsupported'
				 )
				)
			)
			{
			echo '<input type="hidden" name="setamount" value="yes">';
			if ($this->getDataString('type')=='stage_change'&&
					(
					 $this->getDataString('stage')=='terminated' ||
					 $this->getDataString('stage')=='submitted unsupported' ||
					 $this->getDataString('stage')=='rejected'
					 )
					)
				{
				echo '<input type="hidden" name="amount" value=0 />';
				echo '<input type="hidden" name="buspass" value="" />';
				}
			else
				{
				$zot = lcm_fetch_array(lcm_query('select * from lcm_case where id_case ='.$this->getDataInt('id_case')));
				echo "<tr>\n";
				echo '<td valign="top">' . f_err_star('cash') . "Weekly Support:" . "</td>\n";
				echo '<td>';
//				echo '<input name="amount" value="' . clean_output($zot['amount']) . '" class="search_form_txt" />';
				echo '<select name="amount">';
				for ($i=0;$i<=60;$i=$i+5)
					{
					$sel='';
					if (clean_output($zot['amount'])==$i)
						$sel='selected';
					echo '<option value="'.$i.'" '.$sel.'>'.$i.'</option>';
					}
				echo '</select>';
				echo '</td>';
				echo '</tr>';

				echo "<tr>\n";
				echo '<td valign="top">' . f_err_star('cash') . "Bus Pass:" . "</td>\n";
				echo '<td>';
				$checked = ($zot['legal_reason']=='yes'?'checked':'');
				echo '<input type="checkbox" '.$checked.' name="buspass"/>';
				echo '</td>';
				echo '</tr>';
				}
			}


		// +------------------------+
		// | APPOINTMNET CODE BELOW |
		// +------------------------+
	
		if (($this->getDataString('type')=='room_active' 
					||  $this->getDataString('stage')=='supported' 
					||  $this->getDataString('stage')=='accom' 
					|| $this->getDataString('type')=='review'
					|| $this->getDataString('type')=='salreview'
					|| $this->getDataString('bugfix')=='salreview'
					|| $this->getDataString('bugfix')=='review')
				&& $this->getDataString('ctype'))
			{
			if ($this->getDataString('type')=='salreview'||$this->getDataString('bugfix')=='salreview')
				{
				if ($this->getDataString('ctype')=='support')
					$zomg='sup';
				elseif ($this->getDataString('ctype')=='accomidation')
					$zomg='acc';
				echo '<input type="hidden" name="add_appointment" value="'.$zomg.'rev">';	
				echo '<input type="hidden" name="app_title" value="">';
				}
			else
				{
				if ($this->getDataString('ctype')=='support')
					$zomg='sup';
				elseif ($this->getDataString('ctype')=='accomidation')
					$zomg='acc';
				echo "<tr><td>Add Future Event:</td><td>";
				echo "<input type=radio checked name='add_appointment' value='salrev'>SAL Review Date</input><br />";
				echo "<input type=radio name='add_appointment' value='".$zomg."term'>Termination Date</input><br />";
				echo "</td></tr>";
				echo "<!-- Start time -->\n\t\t<tr><td>";
				echo "Event Date:";
				echo "</td><td>"; 
				echo get_date_inputs('app_start',$this->data['app_start_time'], false);
				echo f_err_star('app_start_time');
				echo "</td></tr>\n";
				echo '<input type="hidden" name="app_title" value="">';
				echo "</td></tr>\n";
				}

			}
			
		echo "</table>\n";
		}


function printNoEdit() {
		global $prefs; 
		
		if ($this->getDataInt('id_case'))
			{
			$admin = allowed($this->getDataInt('id_case'), 'a'); // FIXME
			$edit  = allowed($this->getDataInt('id_case'), 'e'); // FIXME
			$write = allowed($this->getDataInt('id_case'), 'w'); // FIXME (put in constructor)
			}
		else
			{
			$admin = 1;
			$edit = 1;
			$write = 1;
			}
		if (!($this->data['id_followup']))
			{
			$zot = $write;
			}
		else	
			{
			$zot = $edit;
			}
		// FIXME: not sure whether this works as previously
		$dis = isDisabled(! ($admin || $zot));
	
		echo '<table class="tbl_usr_dtl" width="99%">' . "\n";
		// Show 'conclusion' options
		if ($this->show_conclusion) {
			$kws_conclusion = get_keywords_in_group_name('conclusion');
			$kws_result = get_keywords_in_group_name('_crimresults');
	
			echo "<tr>\n";
			echo "<td><b>" . _Ti('fu_input_conclusion') . "</b></td>\n";
			echo '<td>';
	
			// Conclusion
			echo '<select ' . $dis . ' name="conclusion" size="1" class="sel_frm">' . "\n";
	
			$default = '';
			if ($this->data['conclusion'])
				$default = $this->data['conclusion'];
			
			echo '<option value=""></option>';	
			foreach ($kws_conclusion as $kw) {
				$sel = isSelected($kw['id_keyword'] == $this->data['outcome']);
				echo '<option ' . $sel . ' value="' . $kw['id_keyword'] . '">' . _T(remove_number_prefix($kw['title'])) . "</option>\n";
			}//MATT WAS HERE. RECORDS "ID_KEYWORD", NOT "NAME" FOR OUTCOME KEYWORD FIELD.
	
			echo "</select>\n";
			echo "</td>\n";
			echo "</tr>\n";
	
//MATT WAS HERE. USED OLD "SENTENCE AMOUNT" BOX FOR OUTCOME AMOUNT
			echo "<tr>\n";
			echo "<td><b>" . _Ti('fu_input_sentence') . "</b></td>\n";
			echo '<td>';
			echo '<input type="text" name="sentence_val" size="10" value="' . $this->data['outcome_amount'] . '" />';
			echo "</td>\n";
			echo "</tr>\n";
		}
	
		//MATT WAS HERE. ADDED && CLAUSE TO TURN "OPENING" FOLLOWUPS INTO NORMAL FOLLOWUPS (LEAVES "CONCLUSION" AS A SPECIAL TYPE)
		if ((_request('submit') == 'set_status' || is_status_change($this->getDataString('type')))&&($this->data['type']=='conclusion'))
		{
			// Change status
			echo "<tr>\n";
			echo "<td>" . _T('case_input_status') . "</td>\n";
			echo "<td>";
			echo _T('kw_followups_' . $this->data['type'] . '_title');
			echo "</td>\n";
			echo "</tr>\n";
			echo '<input type="hidden" name="type" value="' . $this->getDataString('type') . '" />' . "\n";
		}

		echo "<input type='hidden' name='description' value='.".clean_output($this->getDataString('description'))."'>";
		if ($this->show_sum_billed == "yes") {
			echo '<tr>';
			echo '<td>' . _T('fu_input_sum_billed') . "</td>\n";
			echo '<td>';
			echo '<input ' . $dis . ' name="sumbilled" '
				. 'value="' . clean_output($this->getDataString('sumbilled')) . '" '
				. 'class="search_form_txt" size="10" />';

			// [ML] If we do this we may as well make a function
			// out of it, but not sure where to place it :-)
			// This code is also in config_site.php
			$currency = read_meta('currency');
			if (empty($currency)) {
				$current_lang = $GLOBALS['lang'];
				$GLOBALS['lang'] = read_meta('default_language');
				$currency = _T('currency_default_format');
				$GLOBALS['lang'] = $current_lang;
			}
	
			echo htmlspecialchars($currency);
			echo "</td></tr>\n";
		}
		echo "</table><br />\n\n";
	}
}

?>
