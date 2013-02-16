<?php

if (defined('_INC_CRIB')) return
define('_INC_CRIB','1');

function print_cribs($kw = 0)
	{
	print "\n<script language='javascript' type='text/javascript'><!--
		function insertcrib(n)
			{
			nn = 'crib'+n
			document.editfu.description.value = document.editfu.description.value + '\\n' + document.getElementById(nn).title + ' ';
			}
		-->\n</script>";
	if ($kw >0)
		{
		$q="SELECT b.id_crib, b.short, b.full, b.visible, b.id_keyword FROM lcm_crib as b WHERE visible=true and id_keyword = ".$kw;
		}
	else
		{
		$q="SELECT b.id_crib, b.short, b.full, b.visible, b.id_keyword FROM lcm_crib as b";
		}
	$result= lcm_query($q);
	$fix = false;
	while ($row = lcm_fetch_array($result))
		{
		if (!$fix)
			{
//			echo "<tr><td><p>Cribnotes:</p></td><td>";
			echo "| ";
			$fix = true;
			}
		echo "<a id='crib".$row['id_crib']."' href='#' title='".htmlentities($row['full'], ENT_QUOTES)."' onClick='this.href=\"javascript:insertcrib(".$row['id_crib'].")\"'>".$row['short']."</a> | ";	
		}
	if ($fix)
		{
//		echo "</p>";
//		echo "</td></tr>";
		}
	}
?>
