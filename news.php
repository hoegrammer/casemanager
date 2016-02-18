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

if (_request('updated'))
	{
	if (_request('news'))
		{
		write_meta('news',_request('news'));
		write_meta('newsauth',$author_session['name_first']);
		}
	else
		{
		write_meta('news','');
		}
	}

lcm_page_start('News','','','');
matt_page_start('News');
no_tabs();

echo '<form action="news.php" method="post">';

if (_request('updated'))
	{
	lcm_bubble('newnews');
	}
else
	{
	lcm_bubble('news');
	}

echo '<p><textarea name="news" rows="7" cols="100">'.read_meta('news').'</textarea></p>';
echo '<input type="hidden" name="updated" value="yes">';
echo '<button type="submit" class="simple_form_btn">Submit</button>';
echo '</form>';

lcm_page_end();

?>
