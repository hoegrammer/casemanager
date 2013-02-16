<?php

include('inc/inc.php');

global $author_session;
global $prefs;

lcm_page_start('Report II','','','');

switch (_request('matt_type'))
	{
	case 'case':
		$q = "SELECT k.title, count(k.id_keyword) as co FROM lcm_keyword as k RIGHT JOIN lcm_keyword_case as kc on k.id_keyword = kc.id_keyword WHERE id_group="._request('matt_group')." GROUP BY k.id_keyword";
		break;
	case 'client':
		$q = "SELECT k.title, count(k.id_keyword) as co FROM lcm_keyword as k RIGHT JOIN lcm_keyword_client as kcl on k.id_keyword = kcl.id_keyword WHERE id_group="._request('matt_group')." GROUP BY k.id_keyword";
		break;
	case 'followup':
		$q = "SELECT k.title, count(k.id_keyword) as co FROM lcm_keyword as k RIGHT JOIN lcm_keyword_followup as kf on k.id_keyword = kf.id_keyword WHERE id_group="._request('matt_group')." GROUP BY k.id_keyword";
		break;
	}
$result = lcm_query($q);

echo "<table>";
while ($row = lcm_fetch_array($result))
	{
	echo '<tr><td>'.$row['title'].'</td><td>'.$row['co'].'</td></tr>';
	}
echo "</table>";

lcm_page_end;

?>
