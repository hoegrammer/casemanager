<?php
include('inc/inc.php');
include('inc/inc_reports.php');
global $author_session;
global $prefs;

$conditions=false;

lcm_page_start('Reports','','','');

$datetype = _request('datetype');

if ($datetype == 'range')
	{
	$yearstart = _request('date_start_year');
	$monthstart = _request('date_start_month');
	$daystart = _request('date_start_day');
	$yearend = _request('date_end_year');
	$monthend = _request('date_end_month');
	$dayend = _request('date_end_day');
	}
elseif ($datetype == 'today')
	{
	$yearstart = date("Y");
	$monthstart=date("m");
	$daystart = date("d");
	$yearend = date("Y");
	$monthend = date("m");
	$dayend = date("d")+1;
	}
elseif ($datetype == 'lastmonth')
	{
	$yearstart = date("Y");
	$monthstart=date("m");
	$daystart = date("01");
	$yearend = date("Y");
	$monthend = date("m"+1);
	$dayend = date("d")+1;
	}
elseif ($datetype == 'thisyear')
	{
	if (date("m")<4)
		{
		$yearstart = date("Y")-1;
		$yearend = date("Y");
		}
	else
		{
		$yearstart = date("Y");
		$yearend = date("Y")+1;
		}
	$monthstart=date("04");
	$daystart = date("01");
	$monthend = date("04");
	$dayend = date("01");
	}
if ($daystart > 31) {$daystart = 1;$monthstart++;}
if ($dayend > 31) {$dayend = 1;$monthend++;}
if ($monthstart > 12) {$monthstart = 1;$yearstart++;}
if ($monthend > 12) {$monthend = 1;$yearend++;}


if (!$yearstart)
	{
	$yearstart = "1901";
	}
if (!$yearend)
	{
	$yearend = "2099";
	}
if (!$monthstart)
	{
	$monthstart = "04";
	}
if (!$monthend)
	{
	$monthend = "03";
	}
if (!$daystart)
	{
	$daystart = "01";
	}
if (!$dayend)
	{
	$dayend = "31";
	}

show_page_subtitle('Filtered by:');
print "<table class ='tbl_data'>";
$datestart = $yearstart."-".$monthstart."-".$daystart;
$dateend   = $yearend."-".$monthend."-".$dayend;
print "<tr><td><b>Date Range:<b></td><td>From $datestart to $dateend</td></tr>";
if (_request('age'))
	{
	$age = _request('age');
	print "<tr><td><b>Age:</b></td><td>".($age=="new"?"Created":"Updated")."</td></tr>";
	}
if (_request('clfunder'))
	{
	$clfunder = _request('clfunder');
	print "<tr><td><b>Funder Code:</b></td><td>$clfunder</td></tr>";
	}
print "</table><br/>";

foreach ($reports as $report)// FOR EVERY REPORT SELECTED...
	{
	$name=$report['name'];
	if (_request($name)) // ...DO THIS REPORT?
		{
		$title= $report['title'];
		$sql  = $report['sql'];
		show_page_subtitle($title);
		if ($sql)
			{
			$fund = $report['fund'];
			$where = $report['where'];
			if ($report['xtab'])
				{
				$xtab = $report['xtab'];
				$xtab= str_replace("%&DATESTART&%",$datestart,$xtab);
				$xtab= str_replace("%&DATEEND&%",$dateend,$xtab);
				$result = lcm_query($xtab);
				$skip = $true;
				$qtab ="";
				while ($row = lcm_fetch_array($result))
					{
					foreach ($row as $key=>$val)
						{
						if (!$skip)
							{
							$qtab .= $val;
							}
						$skip=!$skip;
						}
					}
				}
			$q= str_replace("%&XTAB&%",$qtab,$sql);
			$q= str_replace("%&DATESTART&%",$datestart,$q);
			$q= str_replace("%&DATEEND&%",$dateend,$q);
			if ($age=="new")
				{
				$q= str_replace("%&AGE&%",$report['new'],$q);
				}
			else
				{
				$q= str_replace("%&AGE&%",$report['old'],$q);
				}

			if ($fund)
				{
				if ($clfunder)
					{
					$q= str_replace("%&FUND&%",$fund, $q);
					$q= str_replace("%&FUNDER&%",$clfunder,$q);
					}
				else
					{
					$q= str_replace("%&FUND&%","", $q);
					}
				}
			if ($where)
				{
				if ($clfunder)
					{
					$q= str_replace("%&WHERE&%",$where, $q);
					}
				else
					{
					$q= str_replace("%&WHERE&%"," 1 ", $q);
					}
				}
			$result = lcm_query($q);
			print '<table class="tbl_data">';
			$first = true;
			while ($row = lcm_fetch_array($result)) // FOR EACH RESULT ROW FOR THAT REPORT:
				{
				if ($first) // IF IT'S THE FIRST ROW...
					{
					print '<tr>';
					$skip = true;
					foreach ($row as $key=>$val) // ...CREATE A TITLE ROW TOO...
						{
						if (!$skip) // ... PROVIDED IT'S NON-DODGY
							{
							print "<td><b>". $key. "</b></td>";
							}
						$skip = !$skip;
						}
					print '</tr>';
					$first=false;
					}
				$skip =true;
				print '<tr>';
				foreach ($row as $key => $val) //FOR EACH VALUE ON THAT ROW...
					{
					if (!$skip) // ...IF IT'S NOT A DODGY ONE
						{
						print "<td>". $val . "</td>";
						}
					$skip=!$skip;
					}
				print '</tr>';
				}
			print '</table>';
			print '<p><small><small>'.$report['note'].'</small></small></p>';
			echo '<br />';
			}










































		if (0)
			{
			if (($select) && ($from))
				{
				$q = 'SELECT ' . $select;
				if ($report['xtab-sel'])
					{
					$xtab_sel = $report['xtab-sel'];
					$xtab_title = $report['xtab-title'];
					$xtab_from = $report['xtab-from'];
					$xtab_where = $report['xtab-where'];
					$xq = "SELECT CONCAT(', SUM(IF($xtab_sel = \"',$xtab_sel,'\",1,0))AS\"',$xtab_title,'\"') FROM $xtab_from";
					if ($xtab_where)
						{
						$xq.= " WHERE ".$xtab_where;
						}
					$result = lcm_query($xq);
					$skip = $true;
					while ($row = lcm_fetch_array($result))
						{
						foreach ($row as $key=>$val)
							{
							if (!$skip)
								{
								$q .= $val;
								}
							$skip=!$skip;
							}
						}
					}
				$q .= ' FROM ' . $from . ' ';
				}
			else
				{			
				lcm_panic("No SELECT/FROM OR SQL");
				}
			if ($join)
				{
				$q .= $join . ' ';
				}
			if ($clfunder)
				{
				if (($type=='client')||($type=='clientkw'))
					{
					$q .= ' , lcm_keyword_client as kcl2 LEFT JOIN lcm_keyword as k2 on kcl2.id_keyword = k2.id_keyword ';
					}
				elseif (($type=='case')||($type=='work'))
					{
					$q .= " , lcm_keyword_client as kcl2 LEFT JOIN lcm_keyword as k2 on kcl2.id_keyword = k2.id_keyword LEFT JOIN lcm_case_client_org as cco2 on kcl2.id_client = cco2.id_client ";
					}
				}	
			$q .= 'WHERE 1 ';
			if ($where)
				{
				$q .= 'AND ' . $where . ' ';
				}
			if ($clfunder)
				{
				if ($type=='client')
					{
					$q .= 'AND cl.id_client = kcl2.id_client AND k2.id_group=24 ';
					}
				elseif ($type=='clientkw')
					{
					$q .= 'AND kcl.id_client = kcl2.id_client AND k2.id_group=24 ';
					}
				elseif ($type=='case')
					{
					$q .= 'AND k2.id_group=24 AND cco2.id_case = c.id_case ';
					}
				elseif ($type=='work')
					{
					$q .= 'AND k2.id_group=24 AND cco2.id_case = fu.id_case ';
					}
				}
			if ($dateend)
				{
				if ($type=='client')
					{
					$q .= 'AND '.($age=="old"?"cl.last_work":"cl.date_creation").' < "' . $dateend . '" ';
					}
				if ($type=='case')
					{
					$q .= 'AND '.($age=="old"?"c.date_update":"c.date_creation").' < "' . $dateend . '" ';
					}
				if ($type=='work')
					{
					$q .= 'AND fu.date_start < "' . $dateend . '" ';
					}
				}
			if ($datestart)
				{
				if ($type=='client')
					{
	//				$q .= 'AND cl.last_work >= "' . $datestart . '" ';
					$q .= 'AND '.($age=="old"?"cl.last_work":"cl.date_creation").' >= "' . $datestart . '" ';
					}
				if ($type=='case')
					{
				$q .= 'AND c.date_update >= "' . $datestart . '" ';
					$q .= 'AND '.($age=="old"?"c.date_update":"c.date_creation").' >= "' . $datestart . '" ';
					}
				if ($type=='work')
					{
					$q .= 'AND fu.date_start >= "' . $datestart . '" ';
					}
				}
			if ($group)
				{
				$q .= 'GROUP BY ' . $group . ' ';
				}
			if ($union)
				{
				$q .= " UNION " .$union;
				}
			}
		}
	}


//foreach ($reports as $report)// FOR EVERY REPORT SELECTED...
//	{
//	$name=$report['name'];
//	$title=$report['title'];
//	if (_request($name)) // ...DO THIS REPORT?
//		{
///		show_page_subtitle($title);
//		$type = $report['type'];
//		$select = $report['select'];
//		$from = $report['from'];
//		$join = $report['join'];
//		$where = $report['where'];
//		$group = $report['group'];
//		$union = $report['union'];
//		$sql = $report['sql'];
///		if ($sql)
////			{
///			$q=$sql;
///			}
///		else
///			{
///			if (($select) && ($from))
///				{
///				$q = 'SELECT ' . $select;
///				if ($report['xtab-sel'])
///					{
///					$xtab_sel = $report['xtab-sel'];
///					$xtab_title = $report['xtab-title'];
///					$xtab_from = $report['xtab-from'];
//					$xtab_where = $report['xtab-where'];
//					$xq = "SELECT CONCAT(', SUM(IF($xtab_sel = \"',$xtab_sel,'\",1,0))AS\"',$xtab_title,'\"') FROM $xtab_from";
//					if ($xtab_where)
//						{
//						$xq.= " WHERE ".$xtab_where;
//						}
//					$result = lcm_query($xq);
//					$skip = $true;
//					while ($row = lcm_fetch_array($result))
//						{
//						foreach ($row as $key=>$val)
//							{
//							if (!$skip)
//								{
//								$q .= $val;
//								}
//							$skip=!$skip;
//							}
//						}
//					}
//				$q .= ' FROM ' . $from . ' ';
//				}
//			else
//				{			
//				lcm_panic("No SELECT/FROM OR SQL");
//				}
//			if ($join)
//				{
//				$q .= $join . ' ';
//				}
//			if ($clfunder)
//				{
//				if (($type=='client')||($type=='clientkw'))
//					{
//					$q .= ' , lcm_keyword_client as kcl2 LEFT JOIN lcm_keyword as k2 on kcl2.id_keyword = k2.id_keyword ';
//					}
//				elseif (($type=='case')||($type=='work'))
//					{
//					$q .= " , lcm_keyword_client as kcl2 LEFT JOIN lcm_keyword as k2 on kcl2.id_keyword = k2.id_keyword LEFT JOIN lcm_case_client_org as cco2 on kcl2.id_client = cco2.id_client ";
//					}
//				}	
//			$q .= 'WHERE 1 ';
//			if ($where)
///				{
//				$q .= 'AND ' . $where . ' ';
//				}
//			if ($clfunder)
//				{
//				if ($type=='client')
//					{
//					$q .= 'AND cl.id_client = kcl2.id_client AND k2.id_group=24 ';
//					}
//				elseif ($type=='clientkw')
//					{
//					$q .= 'AND kcl.id_client = kcl2.id_client AND k2.id_group=24 ';
//					}
//				elseif ($type=='case')
//					{
//					$q .= 'AND k2.id_group=24 AND cco2.id_case = c.id_case ';
//					}
//				elseif ($type=='work')
//					{
//					$q .= 'AND k2.id_group=24 AND cco2.id_case = fu.id_case ';
//					}
//				}
//			if ($dateend)
//				{
//				if ($type=='client')
//					{
//					$q .= 'AND '.($age=="old"?"cl.last_work":"cl.date_creation").' <= "' . $dateend . '" ';
//					}
//				if ($type=='case')
//					{
//					$q .= 'AND '.($age=="old"?"c.date_update":"c.date_creation").' <= "' . $dateend . '" ';
//					}
//				if ($type=='work')
//					{
//					$q .= 'AND fu.date_start <= "' . $dateend . '" ';
//					}
//				}
//			if ($datestart)
//				{
//				if ($type=='client')
//					{
//	//				$q .= 'AND cl.last_work >= "' . $datestart . '" ';
//					$q .= 'AND '.($age=="old"?"cl.last_work":"cl.date_creation").' >= "' . $datestart . '" ';
//					}
//				if ($type=='case')
//					{
//	//				$q .= 'AND c.date_update >= "' . $datestart . '" ';
//					$q .= 'AND '.($age=="old"?"c.date_update":"c.date_creation").' >= "' . $datestart . '" ';
//					}
//				if ($type=='work')
//					{
//					$q .= 'AND fu.date_start >= "' . $datestart . '" ';
//					}
//				}
//			if ($group)
//				{
//				$q .= 'GROUP BY ' . $group . ' ';
//				}
//			if ($union)
//				{
//				$q .= " UNION " .$union;
//				}
//	//		print_r($q);
//			}
//		$result = lcm_query($q);
//		print '<table class="tbl_data">';
//		$first = true;
//		while ($row = lcm_fetch_array($result)) // FOR EACH RESULT ROW FOR THAT REPORT:
//			{
//			if ($first) // IF IT'S THE FIRST ROW...
//				{
//				print '<tr>';
//				$skip = true;
//				foreach ($row as $key=>$val) // ...CREATE A TITLE ROW TOO...
//					{
//					if (!$skip) // ... PROVIDED IT'S NON-DODGY
//						{
//						print "<td><b>". $key. "</b></td>";
//						}
//					$skip = !$skip;
//					}
//				print '</tr>';
//				$first=false;
//				}
//			$skip =true;
//			print '<tr>';
//			foreach ($row as $key => $val) //FOR EACH VALUE ON THAT ROW...
//				{
///				if (!$skip) // ...IF IT'S NOT A DODGY ONE
//					{
//					print "<td>". $val . "</td>";
//					}
//				$skip=!$skip;
//				}
//			print '</tr>';
//			}
//		print '</table>';
//		echo '<br />';
//		}
//	}


lcm_page_end();


?>
