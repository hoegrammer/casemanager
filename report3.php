<?php
include('inc/inc.php');
include('inc/inc_reports.php');
global $author_session;
global $prefs;



lcm_page_start('Report','','','');
if (_request('clfunder'))
	{
	print "LOL"; 
	}


foreach ($reports as $report)// FOR EVERY REPORT SELECTED...
	{
	$name=$report['name'];
	$title=$report['title'];
	if (_request($name)) // ...DO THIS REPORT?
		{
		print $title . '<br/>';
		if ($report['xtab-select'])
			{
			$xtab   = array();
			$xtab_select = $report['xtab-select'];
			$xtab_from = $report['xtab-from'];
			$q = 'SELECT ' .$xtab_select.' FROM '.$xtab_from;
			$result = lcm_query($q);
			while ($row = lcm_fetch_array($result))
				{
				$skip = true;
				foreach ($row as $key => $val)
					{
					if (!$skip)
						{
						array_push ($xtab,$val);
						}
					$skip = !$skip;
					}
				}
			}
		$select = $report['select'];
		$from = $report['from'];
		$join = $report['join'];
		$where = $report['where'];
		$group = $report['group'];
		$xtab_test = $report['xtab-test'];
		if (($select) && ($from))
			{
			$q = 'SELECT ' . $select;
			if ($xtab)
				{
				print_r($xtab);
				$skip = false;
				foreach ($xtab as $val)
					{
					if (!$skip)
						{
						$q .= ", SUM(IF(".$xtab_test."='".$val."',1,0))";	
						}
					else
						{
						$q .= " AS ".$val. " ";
						}
					$skip = !$skip;
					}
				}
			$q .= ' FROM ' . $from . ' ';
			}
		else
			{
			lcm_panic("No SELECT/FROM");
			}
		if ($join)
			{
			$q .= $join . ' ';
			}
		if ($where)
			{
			$q .= 'WHERE ' . $where . ' ';
			}
		if ($group)
			{
			$q .= 'GROUP BY ' . $group . ' ';
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
		}
	}


lcm_page_end;


?>
