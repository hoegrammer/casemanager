<?php
include('inc/inc.php');
include('inc/inc_reports.php');
global $author_session;
global $prefs;

if (!$author_session['right3'])
	{
	lcm_page_start('Archives','','','');
	matt_page_start('Archives');
	no_tabs();
	echo 'Sorry, you really shouldn\'t be here.';
	matt_page_end;
	lcm_page_end;
	exit;
	}

$switch = _request('switch');
if ($switch)
	{
	$file = fopen('/var/www/inc/config/backup',w);
	if ($switch=="on") 
		fwrite($file,'yes');
	else
		fwrite($file,'no');
	fclose($file);
	}

lcm_page_start('Archives','','','');
matt_page_start('Archives');
no_tabs();
lcm_bubble('archive');
echo '<fieldset class="info_box">';
$file = fopen('/var/www/inc/config/backup',r);
$line = fread($file,1);
fclose($file);
if ($line == "y")
	{
	echo "Backups are <b>on</b>. <a href='archive.php?switch=off'>Switch off?</a>";
	}
else
	{
	echo "Backups are <b><font color='red'>off</font></b>. <a href='archive.php?switch=on'>Switch on?</a>";
	}

echo '</fieldset>';
show_page_subtitle ('Logs:');
echo "<p><pre>";
$file = array_reverse(file('/var/www/log/backup.log'));
$limit = 48;
$p[0]='/\[Okay\]/';
$p[1]='/\[Fail\]/';
$p[2]='/\:o\)/';
$p[3]='/\:o\(/';
$r[0]='<b>[Okay]</b>';
$r[1]='<b><font color="red">[Fail]</font></b>';
$r[2]='<b>:o)</b>';
$r[3]='<b><font color="red">:o(</font></b>';
for ($i = $limit-1; $i >= 0; $i--)
	{
	echo preg_replace($p, $r, $file[$i]);
	}
echo '</pre></p>';

lcm_page_end();


?>
