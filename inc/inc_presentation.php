<?php

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

	$Id: inc_presentation.php,v 1.251 2006/10/26 13:16:50 mlutfy Exp $
*/

//
// Execute this file only once
if (defined('_INC_PRESENTATION')) return;
define('_INC_PRESENTATION', '1');

include_lcm('inc_filters');
include_lcm('inc_text');
include_lcm('inc_lang');

use_language_of_visitor();

//
// Header / Footer functions
//

function matt_page_start($title,$size='')
	{
	echo '<div class="matt_page">';
	if ($title)
		{
		echo '<div class="matt_page_title">';
		if ($size=='sub')
			{
			echo '<h4>'.$title.'</h4>';
			echo '</div>';
			}
		else
			{
			echo '<h3 style="text-transform:uppercase">'.$title.'</h3>';
			}
		}
	}

function no_tabs()
	{
	echo '</div>';
	echo '<div class="matt_page_inner">';
	}

function matt_page_end()
	{
	echo '</div>';
	echo '</div>';

	}


// Presentation of the interface, headers and "<head></head>".
// XXX You may want to use lcm_page_start() instead.
function lcm_html_start($title = "AUTO", $css_files = "", $meta = '') {
	global $lcm_lang_rtl, $lcm_lang_left;
	global $mode;
	global $connect_status;
	global $prefs;
		
	$lcm_site_name = clean_input(_T(read_meta('site_name')));
	$title = textebrut($title);

	// Don't show site name (if none) while installation
	if (!$lcm_site_name && $title == "AUTO")
		$lcm_site_name = _T('title_software');

	if (!$charset = read_meta('charset'))
		$charset = 'utf-8';

	@Header("Expires: 0");
	@Header("Cache-Control: no-cache,no-store");
	@Header("Pragma: no-cache");
	@Header("Content-Type: text/html; charset=$charset");
	
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
	<title>". ($lcm_site_name ? $lcm_site_name . " | " : '') . $title ."</title>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=". $charset ."\" />\n";
	echo "$meta\n";

	echo '<link rel="stylesheet" href="Kalendae-master/build/kalendae.css" type="text/css" charset="utf-8">
<script src="Kalendae-master/build/kalendae.standalone.js" type="text/javascript" charset="utf-8"></script>';

	// The 'antifocus' is used to erase default titles such as "New appointment"
	// other functions are used in calendar functions (taken from Spip's presentation.js)
	echo "<script type='text/javascript'><!--
		var title_antifocus = false;

		var memo_obj = new Array();

		function killBackSpace(e)
			{
			e = e? e : window.event;
			var t = e.target? e.target : e.srcElement? e.srcElement : null;
			if(t && t.tagName && (t.type && /(password)|(text)|(file)/.test(t.type.toLowerCase())) || t.tagName.toLowerCase() == 'textarea')
				return true;
			var k = e.keyCode? e.keyCode : e.which? e.which : null;
			if (k == 8)
				{
				if (e.preventDefault)
					e.preventDefault();
				return false;
				};
			return true;
			};

		if(typeof document.addEventListener!='undefined')
			document.addEventListener('keydown', killBackSpace, false);
		else if(typeof document.attachEvent!='undefined')
			document.attachEvent('onkeydown', killBackSpace);
		else
			{
			if(document.onkeydown!=null)
				{
				var oldOnkeydown=document.onkeydown;
				document.onkeydown=function(e)
					{
					oldOnkeydown(e);
					killBackSpace(e);
					};
				}
			else
			document.onkeydown=killBackSpace;
			}














		function findObj(n) { 
			var p, i, x;

			// Check if we have not already memorised this elements
			if (memo_obj[n]) {
				return memo_obj[n];
			}       

			/d = document; 
			if((p = n.indexOf(\"?\"))>0 && parent.frames.length) {
				d = parent.frames[n.substring(p+1)].document; 
				n = n.substring(0,p);
			}       
			if(!(x = d[n]) && d.all) {
				x = d.all[n]; 
			}       
			for (i = 0; !x && i<d.forms.length; i++) {
				x = d.forms[i][n];
			}       
			for(i=0; !x && d.layers && i<d.layers.length; i++) x =
				findObj(n,d.layers[i].document);
			if(!x && document.getElementById) x = document.getElementById(n); 

			// Memorise the element
			memo_obj[n] = x;

			return x;
		}
		
		// [ML] used in inc_calendar.php
		function setvisibility (objet, status) {
			element = findObj(objet);
			if (element.style.visibility != status) {
				if (status == 'flip') {
					if (element.style.visibility == 'visible') {
						element.style.visibility = 'hidden';
					} else {
						element.style.visibility = 'visible';
					}
				} else {
					element.style.visibility = status;
				}
			}
		}
		
		// [ML] used in inc_calendar.php
		function lcm_show(objet) {
			setvisibility(objet, 'visible');
		}

		// [ML] used in inc_calendar.php
		function lcm_hide(objet) {
			setvisibility(objet, 'hidden');
		}
		
		// Used in 'New follow-up -> New activity'
		function display_block (objet, status) {
			element = findObj(objet);
			if (element.style.display != status) {
				if (status == 'flip') {
					if (element.style.display == 'block') {
						element.style.display = 'none';
					} else {
						element.style.display = 'block';
					}
				} else {
					element.style.display = status;
				}
			}
		}
		
		// [ML] Not used at the moment (afaik)
		function display_block_on(objet) {
			display_block(objet, 'block');
		}

		function display_block_off(objet) {
			display_block(objet, 'none');
		}

		//--></script>\n";
	
	echo "	<link rel=\"stylesheet\" type=\"text/css\" href=\"styles/lcm_basic_layout.css\" media=\"screen\" />
	<link rel=\"stylesheet\" type=\"text/css\" href=\"styles/lcm_print.css\" media=\"print\" />\n";

	//
	// Style sheets
	//
	if (! $prefs['theme'])
		$prefs['theme'] = 'green'; // c.f. inc_auth.php, auth()
	
	if (@file_exists("styles/lcm_ui_" . $prefs['theme'] . ".css")) {
		echo "\t" . '<link rel="stylesheet" type="text/css" media="screen" href="styles/lcm_ui_' . $prefs['theme'] . '.css" />' . "\n";
	}
	
	// It is the responsability of the function caller to make sure that
	// the filename does not cause problems...
	$css_files_array = explode(",", $css_files);
	foreach ($css_files_array as $f)
		if ($f)
			echo "\t" . '<link rel="stylesheet" type="text/css" href="styles/lcm_' . $f . '.css" />' . "\n";
	
	// linking the alternate CSS files with smaller and larger font size
	
	// There must be one active font size CSS file
	// [ML] I know this looks silly, but this used to be a big switch.. :-)
	$sys_font_sizes = array(
		"small_font" => "smallfonts",
		"large_font" => "largefonts",
		"medium_font" => "mediumfonts");

	if (isset($prefs['font_size']) && isset($sys_font_sizes[$prefs['font_size']]))
		$usr_font = $sys_font_sizes[$prefs['font_size']];
	else
		$usr_font = "mediumfonts";

	echo '<link rel="stylesheet" type="text/css" media="screen" href="styles/lcm_opt_' . $usr_font . '.css" />' . "\n";

	echo '<link rel="alternate stylesheet" type="text/css" href="styles/lcm_opt_smallfonts.css" title="small_font" />' . "\n";
	echo '<link rel="alternate stylesheet" type="text/css" href="styles/lcm_opt_mediumfonts.css" title="medium_font" />' . "\n";
	echo '<link rel="alternate stylesheet" type="text/css" href="styles/lcm_opt_largefonts.css" title="large_font" />' . "\n";
	
	echo "<link rel=\"shortcut icon\" type=\"image/ico\" href=\"images/lcm/favicon.ico\" />\n";
	echo "<script type=\"text/javascript\" language=\"JavaScript\" src=\"inc/liveUpdater.js\"></script>\n";
	echo "</head>\n";

	// right-to-left (Arabic, Hebrew, Farsi, etc. -- even if not supported at the moment)
	echo '<body' . ($lcm_lang_rtl ? ' dir="rtl"' : '') . ">\n";
}

function lcm_page_start($title = "", $css_files = "", $meta = '', $help_code = '') {
	global $connect_id_auteur;
	global $auth_can_disconnect, $connect_login;
	global $options;
	global $lcm_lang, $lcm_lang_rtl, $lcm_lang_left, $lcm_lang_right;
	global $clean_link;
	global $prefs;
	global $author_session;
	global $connect_status;
	global $connect_allowed;

	$prefs['screen'] = "narrow";
	// Clean the global link (i.e. remove actions passed in the URL)
	$clean_link->delVar('var_lang');
	$clean_link->delVar('set_options');
	$clean_link->delVar('set_couleur');
	$clean_link->delVar('set_disp');
	$clean_link->delVar('set_ecran');

	lcm_html_start($title, $css_files, $meta);
	if (!$connect_allowed)
		{	
		matt_page_start('ACCESS DENIED');
		no_tabs();
		echo "<p>Sorry. This username is not currently allowed to log into case manager. You may be able to use the system as part of a group.<p>";
		echo "<a href=\"lcm_cookie.php?logout=" . htmlspecialchars($author_session['username']) ."\" class=\"prefs_logout\" title=\"" . _T('menu_profile_logout_tooltip') . "\">" . _T('menu_profile_logout') . "</a>";
		matt_page_end();
		exit;
		}

	//
	// Title (mandatory) and description (may be empty) of the site
	//

	$site_name = _T(read_meta('site_name'));
	if (!$site_name)
		$site_name = _T('title_software');

	$site_desc = _T(read_meta('site_description'));
	
	//
	// Most of the header/navigation html
	//

	echo '<div id="header">
		<a href="summary.php" class="balance_link">&nbsp;</a>
		<h1 class="lcm_main_head"><a href="summary.php" class="head_ttl_link">' . $site_name . '</a></h1>
		<div class="lcm_slogan"><a href="summary.php" class="head_subttl_link">' . $site_desc . "</a></div>
		<br />
	</div>\n";
	if (read_meta('news'))
		{
		print "<div class=\"news_box\">". read_meta('news');
		print "<br/><small>By ".read_meta('newsauth')." on ".format_date(read_meta_upd('news'),'short') ."</small></div>";
		}
	
	echo "<div id='wrapper_". $prefs['screen'] ."'>
		<div id=\"container_". $prefs['screen'] ."\">
			<div id=\"content_". $prefs['screen'] ."\">
			<!-- This is the navigation column, usually used for menus and brief information -->
				<div id=\"navigation_menu_column\">
				<!-- Start of navigation_menu_column content -->
					<div class=\"nav_menu_box\">
						<div class=\"nav_column_menu_head\"><div class=\"mm_main_menu\">Infomation</div>
							</div>
						<ul class=\"nav_menu_list\">";
						echo show_navmenu_item("listclients.php", 'main_clients');
						echo show_navmenu_item("listrooms.php", 'main_rooms');
						echo show_navmenu_item("listauthors.php", 'main_authors');
						echo "</ul>\n";
						echo "</div>\n";

echo"
					<div class=\"nav_menu_box\">
						<div class=\"nav_column_menu_head\"><div class=\"mm_main_menu\">Groups</div>
							</div>
						<ul class=\"nav_menu_list\">";
						echo show_navmenu_item("listpannel.php", 'main_pannel');
						echo show_navmenu_item("listaccom.php", 'main_accom');
						echo show_navmenu_item("sal.php", 'main_sal');
						echo show_navmenu_item("welfare_payments.php?tab=print", 'main_tres');
						echo show_navmenu_item("nightshelter.php", 'main_night');
						echo show_navmenu_item("befriender.php", 'main_befriender');
						echo show_navmenu_item("admin.php", 'main_admin');
						echo show_navmenu_item("helpdesk.php", 'main_helpdesk');
						echo "</ul>\n";
						echo "</div>\n";








	if ($connect_status) {
		echo "		
					<div class=\"nav_menu_box\">
						<div class=\"nav_column_menu_head\"><div class=\"mm_admin\">" . _T('menu_admin') . "</div></div>
						<ul class=\"nav_menu_list\">";

//		show_navmenu_item("config_site.php", "admin_siteconf");
		show_navmenu_item("archive.php", "admin_archives");
	//	show_navmenu_item("report.php", "admin_reports");
	//	show_navmenu_item("keywords.php", "admin_keywords");
		show_navmenu_item("news.php", "admin_news");
//		show_navmenu_item("cribnotes.php", "admin_cribnotes");
		echo "</ul>\n";
		echo "</div>\n";
	}

		echo "<div class=\"nav_menu_box\">";
		echo "<div class=\"nav_column_menu_head\"><div class=\"mm_admin\">Search</div></div>";
		show_find_box('client', $find_client_string, '', 'narrow');
		echo "</div>";
		
/*
		echo "<div class=\"nav_menu_box\">";
		echo "<div class=\"nav_column_menu_head\"><div class=\"mm_admin\">Quick Post-it</div></div>";
		echo '<form name="newnote" action="upd_note.php" method="post" onSubmit="var but = document.forms[\'editfu\'][\'submit\']; but.disabled=true; but.innerHTML=\'Please wait\'; return true;">';
		echo '<table>' . "\n";
		echo "<tr><td>";
		echo "</td><td>";
		echo "<select name='cc'>";
		echo "<option value='post-admin'>Admin Team</option>";
		echo "<option value='post-accom'>Accom. Team</option>";
		echo "<option value='post-panel'>Panel</option>";
		echo "<option value='post-sal'>SAL</option>";
		echo "<option value='post-helpd'>Help Desk</option>";
		echo "<option value='post-bef'>Befrienders</option>";
		echo "<option value='post-tres'>Tresuary</option>";
		echo "<option value='post-ns'>Night Shelter</option>";
		echo "</select>";
		echo "</td></tr>";
		echo "<tr><td>";
		echo "</td><td>";
		echo "<select name='colour'>";
		echo "<option value='yellow'>Yellow</option>";
		echo "<option value='blue'>Blue</option>";
		echo "<option value='green'>Green</option>";
		echo "<option value='pink'>Pink</option>";
		echo "</select>";
		echo "</td></tr>";
		echo "<tr><td>";
		echo "</td><td>";
		echo '<textarea  name="description" rows="15" cols="60" class="frm_tarea">';
		echo "</textarea>";
		echo "</td></tr>";
		echo '</table>';
		echo '<input type="hidden" name="ref" value="'.$ref.'">';
		echo "<p><button id=\"mr_submit\" name=\"submit\" type=\"submit\" value=\"submit\" class=\"simple_form_btn\">" . _T('button_validate') . "</button></p>\n";
		echo '</form>';
//		show_find_box('client', $find_client_string, '', 'narrow');
		echo "</div>";
		

*/






		// Show today's date
	echo "\n";
//	echo "<div class=\"nav_menu_box\">
//			<div class=\"nav_column_menu_head\">
//				<div class=\"mm_calendar\">" . _T('menu_calendar') . "</div>
//			</div>";
//MATT WAS HERE, REMOVING APPOINTMENTS/AGENDA/CALANDER FROM SIDE-BAR.
	// Show calendar
//	include_lcm('inc_calendar');
//	$now = date('Y-m-d');
//
//	echo "<table border='0' align='center'><tr><td>\n"; // Temporary? [ML]
//	echo http_calendrier_agenda(mois($now), annee($now), jour($now), mois($now), annee($now), false, 'calendar.php');
//	echo "</td></tr></table>\n";
//
//	echo "</div>\n";
	
	// Start agenda box
 

/*	echo '<div class="nav_menu_box">' . "\n";
		echo '<div class="nav_column_menu_head">';
			echo '<div class="mm_agenda">'. _T('menu_agenda') . '</div>
			</div>';
	$events = false;

	


	// Show past appointments
	$q = "SELECT a.id_app, a.start_time, a.type, a.title
		FROM lcm_app as a, lcm_author_app as aa
		WHERE (aa.id_author = " . $GLOBALS['author_session']['id_author'] . "
			AND a.id_app = aa.id_app
			AND a.start_time < '" . date('Y-m-d') ."')
			AND a.dismissed = 0
		ORDER BY a.reminder ASC
		LIMIT 5";

	$result = lcm_query($q);

	if (lcm_num_rows($result)>0) {
		$events = true;//ZOTON
		echo "<div class=\"evil\">";
		echo "<p class=\"nav_column_text\">\n";
		echo "<strong>" . _T('calendar_button_goneapps') . "</strong><br />\n";
		echo "</p>\n";
		
		echo "<ul class=\"small_agenda\">\n";
		while ($row=lcm_fetch_array($result)) {
			echo "<li><blink><a href=\"app_det.php?app=" . $row['id_app'] . "\">"
				. format_date($row['start_time'],'date_short') . " - " . $row['title'] . "</a></blink></li>\n";
		}
		echo "</ul>\n";
		echo "</div>";
	}


	// Show appointments for today
	$q = "SELECT app.id_app, start_time, type, title
			FROM lcm_app as app, lcm_author_app as aut
			WHERE aut.id_author=" . $GLOBALS['author_session']['id_author'] . "
				AND app.id_app = aut.id_app
				AND " . lcm_query_trunc_field('app.start_time', 'day') . "
					= " . lcm_query_trunc_field('NOW()', 'day') . "
				AND app.dismissed = 0
			ORDER BY app.reminder ASC";

	$result = lcm_query($q);

	if (lcm_num_rows($result) > 0) {
		echo "<div class=\"lesseroftwoevils\">";
		$events = true;
		$today = getdate(time());
//MATT WAS HERE. DON't LINK TO CALANDER FROM AGENDA		
		echo "<p class=\"nav_column_text\">\n"
			. "<strong>"._Th('calendar_button_now')."</strong><br/>\n";
//			. '<strong><a class="content_link" href="calendar.php?type=jour'
//			. "&amp;jour=" . $today['mday']
//			. "&amp;mois=" . $today['mon']
//			. "&amp;annee=" . $today['year'] . '">'
//			. _Th('calendar_button_now') . "</a></strong><br />\n";
		echo "</p>\n";
		echo "<ul class=\"small_agenda\">\n";
		while ($row=lcm_fetch_array($result)) {
			echo "<li><a href=\"app_det.php?app=" . $row['id_app'] . "\">"
				//MATT WAS HERE, DON'T SHOW DATE/TIME ON TODAY'S AGENDA
				//. heures($row['start_time']) . ':' . minutes($row['start_time']) . " - "
				. $row['title'] . "</a></li>\n";
		}
		echo "</ul>\n";
		echo "<hr class=\"hair_line\" />\n";
		echo "</div>";
	}

	// Show next appointments
	$q = "SELECT a.id_app, a.start_time, a.type, a.title
		FROM lcm_app as a, lcm_author_app as aa
		WHERE (aa.id_author = " . $GLOBALS['author_session']['id_author'] . "
			AND a.id_app = aa.id_app
			AND a.start_time > '" . date('Y-m-d') ."')
			AND a.dismissed = 0
		ORDER BY a.reminder ASC
		LIMIT 5";

	$result = lcm_query($q);

	if (lcm_num_rows($result)>0) {
		$events = true;
		echo "<p class=\"nav_column_text\">\n";
		echo "<strong>" . _T('calendar_button_nextapps') . "</strong><br />\n";
		echo "</p>\n";
		
		echo "<ul class=\"small_agenda\">\n";
		while ($row=lcm_fetch_array($result)) {
			echo "<li><a href=\"app_det.php?app=" . $row['id_app'] . "\">"
				. format_date($row['start_time'],'date_short') . " - " . $row['title'] . "</a></li>\n";
		}
		echo "</ul>\n";
	}

	if (!$events) {
		echo '<p class="nav_column_text">' . _T('calendar_info_noacts') . "</p>\n";
	}
	
	// my appointments
	echo '&nbsp;<a href="author_det.php?tab=appointments&amp;author=' . $GLOBALS['author_session']['id_author'] . '" title="' . _T('title_agenda_list') . '">'
		. '<img src="images/jimmac/stock_show-form-dialog.png" border="0" width="16" height="16" alt="" /></a>';
//MATT WAS HERE. DON'T ALLOW CREATION OF AN AGENDA ITEM OUTSIDE OF FOLLOWUP PAGE.	
	// new appointment
//	echo '&nbsp;<a href="edit_app.php?app=0" title="' . _T('app_button_new') . '">'
//		. '<img src="images/jimmac/stock_new-16.png" border="0" width="16" height="16" alt="" /></a>';

	// End of nav_menu_box for Agenda
	echo "</div>\n";*/

	// End of "navigation_menu_column" content
	echo "</div>

				<!-- The main content will be here - all the data, html forms, search results etc. -->
				<div id=\"main_column\">
				
					<!-- Start of 'main_column' content -->";
//					<h3 class=\"head_box\">";
// MATT WAS HERE, REMOVING HELP QUESTION MARKS	
//	if ($help_code)
//		echo '<span class="help_icon">' . lcm_help($help_code) . "</span> ";
					
//	echo $title;
//	echo "</h3>
echo "				<!-- [KM] Just a small experiment how the future breadcrumb will look like -->
					<!-- div id=\"breadcrumb\"><a href=\"#\" title=\"Test link\">Home</a> &gt; <a href=\"#\" title=\"Test link\">Page1</a> &gt; <a href=\"#\" title=\"Test link\">Subpage1</a> &gt; Subsubpage1</div -->
	";





}

// Footer of the interface
// XXX You may want to use lcm_page_end() instead
function lcm_html_end() {
	// Create a new session cookie if the IP changed
	// An image is sent, which then calls lcm_cookie.php with Javascript
	if ($_COOKIE['lcm_session'] && $GLOBALS['author_session']['ip_change']) {
		echo "<img name='img_session' src='images/lcm/nothing.gif' width='0' height='0' />\n";
		echo "<script type='text/javascript'><!-- \n";
		echo "document.img_session.src='lcm_cookie.php?change_session=oui';\n";
		echo "// --></script>\n";
	}

	flush();
}


function lcm_page_end($credits = '') {
	global $lcm_version_shown;
	global $connect_id_auteur;

	global $author_session;
	global $find_org_string;
	global $find_case_string;
	global $find_client_string;
	global $find_add_string;	
	global $prefs;
	//[KM] The bottom of a single page
	//
	echo "
				<!-- End of 'main_column' content -->
				</div>
			</div>
		</div>\n";

	// [KM] The right and the left column can be very long, so, we can put here a 
	// lot of additional information, some tiny help hints and so.
	echo "<div id=\"prefs_column\">\n";
	echo "<!-- Start of 'prefs_column' content -->\n";

	// Checking for "wide/narrow" user screen
	if($prefs['screen'] == "wide") {
		echo "<div class=\"prefs_column_menu_head\">
				<div class=\"sm_profile\">" . _T('menu_profile') . "</div>
			</div>
			<p class=\"prefs_column_text\">"
				. '<a href="author_det.php?author=' . $author_session['id_author'] . '" class="prefs_normal_lnk"'
				. ' title="' . _T('case_tooltip_view_author_details', array('author' => htmlspecialchars(get_person_name($author_session)))) . '">' . get_person_name($author_session)
				. "</a><br /><br />
			<a href=\"config_author.php\" class=\"prefs_myprefs click_btn\">" . _T('menu_profile_preferences') . "</a><br /><br />
			<a href=\"lcm_cookie.php?logout=" . htmlspecialchars($author_session['username']) ."\" class=\"prefs_logout\" title=\"" . _T('menu_profile_logout_tooltip') . "\">" . _T('menu_profile_logout') . "</a>
			</p><br />
			<div class=\"prefs_column_menu_head\">
				<div class=\"sm_search\">" . _T('menu_search') . "</div>
			</div>\n";

		//
		// Search/find boxes
		//
//MATT WAS HERE. REMOVE "FIND CASE" SEARCH BOX FROM ALL PAGES

//		lcm_bubble('search');
		show_find_box('client', $find_client_string, '', 'narrow');
		show_find_box('case', $find_case_string, '', 'narrow');
		show_find_box('add', $find_add_string, '', 'narrow');
	
		echo "<br />
			<div class=\"prefs_column_menu_head\"><div class=\"sm_font_size\">" . _T('menu_fontsize') . "</div>
			</div>
			<ul class=\"font_size_buttons\">
				<li><a href=\"javascript:;\" title=\"Small Text\" onclick=\"setActiveStyleSheet('small_font')\">A-</a></li>
				<li><a href=\"javascript:;\" title=\"Normal Text\" onclick=\"setActiveStyleSheet('medium_font')\">A</a></li>
				<li><a href=\"javascript:;\" title=\"Large Text\" onclick=\"setActiveStyleSheet('large_font')\">A+</a></li>
			</ul>\n";
	} else {
		// Data from the refs_column - user name, links [My preferences] & [Logout]
		echo "<div id=\"user_info_box_large_screen\">";
		echo "<p class=\"prefs_column_text\">"
				. '<a href="author_det.php?author=' . $author_session['id_author'] . '" class="prefs_normal_lnk"'
				. ' title="' . _T('case_tooltip_view_author_details', array('author' => htmlspecialchars(get_person_name($author_session)))) . '">' . get_person_name($author_session)
				. "</a><br /><br />
			<a href=\"config_author.php\" class=\"prefs_myprefs click_btn\">" .  _T('menu_profile_preferences') . "</a><br /><br /><a href=\"javascript:;\" title=\"Small Text\" onclick=\"setActiveStyleSheet('small_font')\" class=\"set_fnt_sz\">&nbsp;A-&nbsp;</a>&nbsp;
				<a href=\"javascript:;\" title=\"Normal Text\" onclick=\"setActiveStyleSheet('medium_font')\" class=\"set_fnt_sz\">&nbsp;A&nbsp;&nbsp;</a>&nbsp;
				<a href=\"javascript:;\" title=\"Large Text\" onclick=\"setActiveStyleSheet('large_font')\" class=\"set_fnt_sz\">&nbsp;A+&nbsp;</a>&nbsp;&nbsp;"
				. "<a href=\"lcm_cookie.php?logout=" . htmlspecialchars($author_session['username']) ."\" class=\"prefs_logout click_btn\" title=\"" . _t('menu_profile_logout_tooltip') . "\">" . _t('menu_profile_logout') . "</a>
			</p>"; // TRAD (Small, Normal, Large text) ZATMEG
		echo "</div>";
	}

	echo "<!-- End of \"prefs_column\" content -->\n";
	echo "</div>\n";

	//just test...
	echo "<div class=\"clearing\">&nbsp;</div>\n";
	echo "</div>\n";

//	if($prefs['screen'] == "narrow") { //ZOTON
//		echo '<div id="footer_narrow">
//		<div class="prefs_column_menu_head"><div class="sm_search">' .  _T('menu_search') . "</div></div>
//		<table border=\"0\" align=\"center\" width=\"100%\">
//			<tr>
//				<td align=\"left\" width=\"33%\" valign=\"top\">\n";
//	
//		//
//		// Search/find boxes
//		//
//	
//		echo "</td>\n";
//		echo '<td align="left" width="33%" valign="top">';
// MATT COME BACK HERE		
//		show_find_box('client', $find_client_string, '', 'narrow');
//	
//		echo "</td>\n";
//		echo '<td align="left" width="33%" valign="top">';
//	
//	
//		echo "</td>
//			</tr>
//		</table>
//		</div><br />\n";
//	}

	echo "<div id=\"footer\">". _T('title_software') ." (". $lcm_version_shown .")<br/> ";
	echo _T('info_free_software', 
			array(
				'distributed' => '<a href="http://www.nrcentre.org.uk/" class="prefs_normal_lnk">' . _T('info_free_software1') . '</a>',
				'license' => lcm_help_string('about_license', _T('info_free_software2'))))
		. "</div>\n";

	if ($GLOBALS['debug'])
		echo "<p align='left'>Debug (" . $GLOBALS['debug'] . "): SQL Queries: " . $GLOBALS['db_query_count'] . "</p>\n";

	echo "</body>\n";
	echo "</html>\n";

	// [ML] Off-topic note, seen while removing code:
	// http://www.dynamicdrive.com/dynamicindex11/abox.htm

	lcm_html_end();
}

/*
 * Header function for the installation
 * They are used by install.php and lcm_test_dirs.php
 */
function install_html_start($title = 'AUTO', $css_files = '', $dbg = '') {
	global $lcm_lang_rtl;

	if ($dbg)
		lcm_log("$dbg: start", 'install');

	if ($title == 'AUTO')
		$title = _T('install_title_installation_start');

	$css_files = ($css_files ? $css_files . ",install" : "install");

	lcm_html_start($title, $css_files);

	echo "\t<br/>\n";
	echo "\t<div align='center' id='install_screen'>\n";
	echo "\t\t<h1><b>$title</b></h1>\n";

	echo "\n<!-- END install_html_start() -->\n\n";
}

/*
 * Footer function for the installation
 * They are used by install.php and lcm_test_dirs.php
 */
function install_html_end($dbg = '') {
	if ($dbg)
		lcm_log("$dbg: end", 'install');

	echo "</div>
	</body>
	</html>\n\n";
}

//
// Help
//

function lcm_help($code, $anchor = '') {
	global $lcm_lang;

	$topic = _T('help_title_' . $code);
	if ($anchor) $anchor = '#' . $anchor;

	return '<a href="lcm_help.php?code=' . $code . $anchor .'" target="lcm_help" ' 
		. 'onclick="javascript:window.open(this.href, \'lcm_help\', \'scrollbars=yes, resizable=yes, width=740, height=580\'); return false;">'
		. '<img src="images/lcm/help.png" alt="help: ' . $topic . '" '
		. 'title="help: ' . $topic . '" width="12" height="12" border="0" align="middle" /> '
		. "</a>\n";
}

function lcm_bubble($code) {
	echo '<div class="small_help_box">' . _T('help_intro_' . $code) . "</div>\n";
}

// shows help link for a string rather than for icon (see GPL notice in install + footer)
function lcm_help_string($code, $string, $anchor = '') {
	global $lcm_lang;

	$topic = _T('help_title_' . $code);
	if ($anchor) $anchor = '#' . $anchor;

	return '<a class="prefs_normal_lnk" href="lcm_help.php?code=' . $code . $anchor . '" target="lcm_help" ' 
		. 'onclick="javascript:window.open(this.href, \'lcm_help\', \'scrollbars=yes, resizable=yes, width=740, height=580\'); return false;">'
		. $string
		. "</a>";
}


//
// Help pages HTML header & footer
//

function get_help_page_toc() {
	$toc = array(
		'installation' => array('install_permissions', 'install_database', 'install_personal'),
		'cases' => array('cases_intro', 'cases_participants', 'cases_followups', 'cases_statusstage'),
		'clients' => array('clients_intro', 'clients_newclient', 'clients_neworg'),
		'authors' => array('authors_intro', 'authors_edit', 'authors_admin'),
		'tools' => array('tools_agenda', 'tools_documents'),
		'siteconfig' => array('siteconfig_general', 'siteconfig_collab', 'siteconfig_policy', 'siteconfig_regional'),
		'archives' => array('archives_intro', 'archives_export', 'archives_import'),
		'reports' => array('reports_intro', 'reports_edit'), 
		'keywords' => array('keywords_intro', 'keywords_new_group', 'keywords_new', 'keywords_remove'),
		'about' => array('about_contrib', 'about_license')); 

	return $toc;
}

function help_page_start($page_title, $code = '') {

	if (!$charset = read_meta('charset'))
		$charset = 'utf-8';

	$toc = get_help_page_toc();

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>' . _T('help_title_help') . '</title>
<meta http-equiv="Content-Type" content="text/html; charset=' . $charset . '" />
<link rel="stylesheet" href="styles/lcm_help.css" type="text/css" />
<script type="text/javascript" language="JavaScript" src="inc/help_menu.js"></script>
</head>' . "\n";

	echo "<body>\n";
	echo '<h1>' . _T('help_title_help') . "</h1>\n";
	echo '<div id="hlp_big_box">' . "\n";
	echo '<div id="hlp_menu">' . "\n";
	echo '<ul id="nav">' . "\n";

	foreach ($toc as $topic => $subtopics) {
		echo '<li><a href="lcm_help.php?code=' . $topic .'">' . _T('help_title_' . $topic) . '</a>' . "\n";
		echo '<ul class="subnav">';

		foreach ($subtopics as $st)
			echo '<li><a href="lcm_help.php?code=' . $st .'">' . _T('help_title_' . $st) . '</a></li>' . "\n";

		echo "</ul>\n";
		echo "</li>\n";
	}
	
	echo "</ul>\n"; // closes id="nav"
	echo "</div>\n"; // closes id="hlp_menu"

	echo '<div id="hlp_cont">' . "\n";
	echo '<div class="hlp_data">' . "\n";

	foreach ($toc as $topic => $subtopics) {
		foreach ($subtopics as $key => $val) {
			if ($val == $code) {
				echo '<div id="breadcrumb">' 
					. '<a href="lcm_help.php?code=' . $topic . '">' . _T('help_title_' . $topic) .  '</a>'
					. "</div>\n";
			}
		}
	}

	echo '<h2>' . $page_title . "</h2>\n";
}

function help_page_end() {
	echo '<p class="normal_text">&nbsp;</p>' . "\n";

	// TODO: add next/previous sections?

	echo "</div>\n"; // closes class="hlp_data"
	echo "</div>\n"; // closes id="hlp_cont"
	echo "</div>\n"; // closes id="hlp_big_box"
	echo "</body>\n";
	echo "</html>\n\n";
}


//
// Commonly used visual functions
//

function get_date_inputs($name = 'select', $date = '', $blank = true, $table = false, $noday = false) {
	// $table parameter above is deprecated

	// Extract date values
	// First check in session variable (if error), fallback on $date
	$split_date = recup_date($date);
	$default_month = _session($name . '_month', $split_date[1]);
	$default_day   = _session($name . '_day',   $split_date[2]);
	$default_year  = _session($name . '_year',  $split_date[0]);

	if ($default_day == '0' || $default_day == '00')
		$default_day = '';

	if ($default_year == '0000')
		$default_year = '';

	// If name is empty, disable fields
	$dis = (($name) ? '' : 'disabled="disabled"');

	if ($noday)
		{
		$ret = "<input type='hidden' value='01' name='".$name."_day' />";
		}
	else
		{
		$ret = "<input size=\"4\" type=\"text\" $dis value=\"$default_day\" name=\"" . $name . "_day\" id=\"" . $name . "_day\" />\n";
		}
	// Month of year
	$ret .= "<select $dis name=\"" . $name . "_month\" id=\"" . $name . "_month\">";

	for ($i = 1; $i <= 12; $i++) {
		$default = isSelected($i == $default_month);
		$ret .= "<option" . $default . " value=\"" . $i . "\">" .  _T('date_month_' . $i) . "</option>";
	}

	if ($blank) {
		$default = isSelected($default_month == 0);
		$ret .= '<option' . $default . ' value=""></option>';
	}

	$ret .= "</select>\n";

	// Year
	$ret .= "<input size=\"4\" type=\"text\" $dis value=\"$default_year\" name=\"" . $name . "_year\" id=\"" . $name . "_year\" />\n";

	return $ret;
}

function get_time_inputs($name = 'select', $time = '', $hours24 = true, $show_seconds = false, $table = false, $now = false) {
	// table parameter above is deprecated
	
	if ($now) {
		$default_hour = date('H');
		$default_minutes = date('i');
		$default_seconds = date('s');
	} else {
		$split_time = recup_time($time);
	        $default_hour = $split_time[0];
        	$default_minutes = $split_time[1]; //- ($split_time[1] % 5); // make it round
	        $default_seconds = $split_time[2];
	}
	
	// If name is empty, disable fields
	$dis = (($name) ? '' : 'disabled="disabled"');

	// Hour
	$ret = "<select $dis name=\"" . $name . "_hour\" id=\"" . $name . "_hour\">\n";

	for ($j = -1; $j < 24; $j++) {
		$i=$j+1;
		$default = ($i == $default_hour ? ' selected="selected"' : '');
		$ret .= "<option" . $default . " value=\"" . sprintf('%02u',$i) . "\">";
		if ($hours24) {
			$ret .= $i;
		} else {
			$ret .= gmdate('g a',($i * 3600));
		}
		$ret .= "</option>";
	}

	$ret .= "</select>\n";
	// Minutes
	// MATT WAS HERE. TOTALLY CHANGED MINUITES TO INCLUDE CURRENT MINUTE, RATHER THAN TO THE NEAREST X
	$ret .= ":<select $dis name=\"" . $name . "_minutes\" id=\"" . $name . "_minutes\">\n";
	for ($j = -1; $j < 60; $j=$j+1) 
		{
		$i=$j+1;
		if (($default_minutes<$i)&&($default_minutes>$i-5))
			{
			$ret .= "<option selected=true value=".sprintf('%02u',$default_minutes).">".sprintf('%02u',$default_minutes)."</option>";	
			}
		if ($i != $default_minutes)
			{
			$ret .= "<option" . $default . " value=\"" . sprintf('%02u',$i) . "\">" . sprintf('%02u',$i) . "</option>";
			}
		else
			{
			$ret .= "<option selected=true value=\"" . sprintf('%02u',$i) . "\">" . sprintf('%02u',$i) . "</option>";
			}
		}

	$ret .= "</select>\n";

	// Seconds
//	if (($show_seconds)&&(1)) {
//		$ret .= ":<select hidden $dis name=\"" . $name . "_seconds\" id=\"" . $name . "_seconds\">\n";
//
//		for ($i = 0; $i < 60; $i++) {
//			$default = ($i == $default_seconds ? ' selected="selected"' : '');
//			$ret .= "<option" . $default . " value=\"" . sprintf('%02u',$i) . "\">" . sprintf('%02u',$i) . "</option>";
//		}
//
//		$ret .= "</select>\n";
//	}

	
	return $ret;
}

function get_time_interval_inputs($name = 'select', $time) {
	global $prefs;

	$ret = '';
	$hours_only = ($prefs['time_intervals_notation'] == 'hours_only');

	if ($hours_only) {
		$days = 0;
		$hours = $time / 3600;
		$minutes = 0;
	} else {
		$days = (int) ($time / 86400);
		$hours = (int) ( ($time % 86400) / 3600);
		$minutes = (int) round( ($time % 3600) / 300) * 5;
	}

	// If name is empty, disable fields
	$dis = isDisabled(! $name);

	// Days
	if ($hours_only) {
		$ret .= "<input type=\"hidden\" name=\"" . $name . "_days\" id=\"" . $name . "_days\" value=\"$days\" />\n";
	} else {
		$ret .= "<input $dis size=\"4\" name=\"" . $name . "_days\" id=\"" . $name . "_days\" align=\"right\" value=\"$days\" />";
		$ret .= "&nbsp;" . _T('time_info_short_day') . ", ";
	}

	// Hour
	$ret .= "<input $dis size=\"4\" name=\"" . $name . "_hours\" id=\"" . $name . "_hours\" align=\"right\" value=\"$hours\" />";
	$ret .= _T('time_info_short_hour') . "\n";
	
	// Minutes
	if ($hours_only) {
		$ret .= "<input type=\"hidden\" name=\"" . $name . "_minutes\" id=\"" . $name . "_minutes\" value=\"$minutes\" />\n";
	} else {
		$ret .= "<input $dis size=\"2\" name=\"" . $name . "_minutes\" id=\"" . $name .  "_minutes\" value=\"$minutes\" />\n";
		$ret .= "&nbsp;" . _T('time_info_short_min') . "\n";
	}

	return $ret;
}

// [ML] In order to re-use the code from the previous function
// They should probably be split into many smaller functions.
// And since we have many such functions, it would not be bad
// to put them in their own include file..
function get_time_interval_inputs_from_array($name = 'select', $source) {
	global $prefs;
	$ret = '';

	$hours_only = ($prefs['time_intervals_notation'] == 'hours_only');

	$days = $source[$name . '_days'];
	$hours = $source[$name . '_hours'];
	$minutes = $source[$name . '_minutes'];

	// If name is empty, disable fields
	$dis = isDisabled(! $name);

	// Days
	if ($hours_only) {
		$ret .= "<input type=\"hidden\" name=\"" . $name . "_days\" id=\"" . $name . "_days\" value=\"$days\" />\n";
	} else {
		$ret .= "<input $dis size=\"4\" name=\"" . $name . "_days\" id=\"" . $name . "_days\" align=\"right\" value=\"$days\" />";
		$ret .= "&nbsp;" . _T('time_info_short_day') . ", ";
	}

	// Hour
	$ret .= "<input $dis size=\"4\" name=\"" . $name . "_hours\" id=\"" . $name . "_hours\" align=\"right\" value=\"$hours\" />";
	$ret .= _T('time_info_short_hour');
	
	// Minutes
	if ($hours_only) {
		$ret .= "<input type=\"hidden\" name=\"" . $name . "_minutes\" id=\"" . $name . "_minutes\" value=\"$minutes\" />\n";
	} else {
		$ret .= "<input $dis size=\"2\" name=\"" . $name . "_minutes\" id=\"" . $name .  "_minutes\" value=\"$minutes\" />\n";
		$ret .= "&nbsp;" . _T('time_info_short_min');
	}

	return $ret;
}

// Returns an array with valid CSS files for themes (lcm_ui_*.css)
function get_theme_list() {
	$list = array();

	$handle = opendir("styles");
	while (($f = readdir($handle)) != '') {
		if (is_file("styles/" . $f)) {
			// matches: styles/lcm_ui_foo.css
			if (preg_match("/lcm_ui_([_a-zA-Z0-9]+)\.css/", $f, $regs)) {
				// push_array($list, $regs[1]);
				$list[$regs[1]] = $regs[1];
			}
		}
	}

	ksort($list);
	reset($list);

	return $list;
}

// Returns a "select" with choice of yes/no
function get_yes_no($name, $value = '') {
	$ret = '';

	// [ML] sorry for this stupid patch, practical for keywords.php
	$val_yes = 'yes';
	$val_no = 'no';

	if ($value == 'Y' || $value == 'N') {
		$val_yes = 'Y';
		$val_no  = 'N';
	}

	$yes = isSelected($value == $val_yes);
	$no = isSelected($value == $val_no);
	$other = isSelected(! ($yes || $no));

	// until we format with tables, better to keep the starting space
	$ret .= ' <select name="' . $name . '" class="sel_frm">' . "\n";
	$ret .= '<option value="' . $val_yes . '"' . $yes . '>' . _T('info_yes') . '</option>';
	$ret .= '<option value="' . $val_no  . '"' . $no .  '>' . _T('info_no') . '</option>';

	if ($other)
		$ret .= '<option value=""' . $other . '> </option>';

	$ret .= '</select>' . "\n";

	return $ret;
}

// Returns a "select" with choice of yes(opt)/yes(mandatory)/no
function get_yes_no_mand($name, $value = '') {
	$ret = '';

	// [ML] sorry for this stupid patch, practical for keywords.php
	$yes_opt = isSelected($value == 'yes_optional');
	$yes_mand = isSelected($value == 'yes_mandatory');
	$no = isSelected($value == 'no');
	$other = isSelected(! ($yes_mand || $yes_opt || $no));

	// until we format with tables, better to keep the starting space
	$ret .= ' <select name="' . $name . '" class="sel_frm">' . "\n";
	$ret .= '<option value="yes_optional"' . $yes_opt . '>' . _T('info_yes_optional') . '</option>';
	$ret .= '<option value="yes_mandatory"' . $yes_mand . '>' . _T('info_yes_mandatory') . '</option>';
	$ret .= '<option value="no"' . $no .  '>' . _T('info_no') . '</option>';

	if ($other)
		$ret .= '<option value=""' . $other . '> </option>';

	$ret .= '</select>' . "\n";

	return $ret;
}

// Show tabs
function show_tabs($tab_list, $selected, $url_base) {
// $tab_list = array( tab1_key => tab1_name, ... )
// $selected = tabX_key
// $url_base = url to  link tabs to as 'url'?tab=X

	// Get current $url_base parameters
	$params = array();
	$pos = strpos($url_base,'?');
	if ($pos === false) {
		$query = '';
	} else {
		$query = substr($url_base,$pos+1);
		$url_base = substr($url_base,0,$pos);
		parse_str($query,$params);
		unset($params['tab']);
		foreach ($params as $k => $v) {
			$params[$k] = $k . '=' . urlencode($v);
		}
	}
	
	echo "<!-- Page tabs generated by show_tabs() -->\n";

	// [KM]
	echo "<div class=\"tabs\">\n";
	echo "<ul class=\"tabs_list\">\n";
	
	// Display tabs
	foreach($tab_list as $key => $tab) {
		if ($key != $selected) {
			$a_title = "";
			if (is_array($tab))
				$a_title = 'title="' . $tab['tooltip'] . '"';

			echo "\t<li><a $a_title href=\"$url_base?";
			if (count($params)>0) echo join('&amp;',$params) . '&amp;';
			echo 'tab=' . $key . "\">";
		} else {
			echo "\t<li class=\"active\">";
		}

		if (is_array($tab))
			echo $tab['name'];
		else
			echo $tab;

		if ($key != $selected) echo "</a>";
		echo "</li>\n";
	}
	
	echo "</ul>";
	echo "</div>";
	echo "</div>";
	echo '<div class="matt_page_inner">';
	echo "\n\n";
}

// Show tabs & links
function show_tabs_links($tab_list, $selected='', $sel_link=false) {
// $tab_list = array( tab1_key => array('name' => tab1_name, 'url' => tab1_link), ... )
// $selected = tabX_key;
// $sel_link - link of the selected tab active (true/false)

	echo "<!-- Page tabs generated by show_tabs_links() -->\n";

	// [KM]
	echo "<div class=\"tabs\">\n";
	echo "<ul class=\"tabs_list\">\n";
	
	// Display tabs
	foreach($tab_list as $key => $tab) {
		if ($key === $selected) {
			echo "\t<li class=\"active\">";
			if ($sel_link) echo "<a href=\"" . $tab['url'] . "\">";
		} else echo "\t<li><a href=\"" . $tab['url'] . "\">";
		echo $tab['name'];
		if ($sel_link || !($key === $selected) ) echo "</a>";
		echo "</li>";
		echo "\n";
	}

	echo "</ul>";
	echo "</div>";
	echo "\n\n";
}

// XXX this does not work
/*
function get_list_pos(&$result) {
	$list_pos = 0;
	
	if (isset($_REQUEST['list_pos']))
		$list_pos = $_REQUEST['list_pos'];
	
	if ($list_pos >= $number_of_rows)
		$list_pos = 0;
	
	// Position to the page info start
	if ($list_pos > 0)
		if (!lcm_data_seek($result, $list_pos))
			lcm_panic("Error seeking position $list_pos in the result");
	
	return $list_pos;
}
*/

function show_list_start($headers = array()) {
	echo '<table border="0" align="center" class="tbl_usr_dtl" width="99%">' . "\n";
	echo "<tr>\n";

	foreach($headers as $h) {
		$width = (isset($h['width']) ? ' width="' . $h['width'] . '" ' : '');
		echo '<th ' . $width . 'class="heading" nowrap="nowrap">';

		if ($h['order'] && $h['order'] != 'no_order') {
			$ovar = $h['order']; // on which variable to order
			$cur_sort_order = "";
//			$cur_sort_order = (isset($h['default']) ? $h['default'] : "");

			$new_sort_order = '';

			if (isset($_REQUEST[$ovar]) && ($_REQUEST[$ovar] == 'ASC' || $_REQUEST[$ovar] == 'DESC'))
				$cur_sort_order = $_REQUEST[$ovar];

			if (! $cur_sort_order || $cur_sort_order == 'ASC')
				$new_sort_order = 'DESC';
			else
				$new_sort_order = 'ASC';
//			if ($new_sort_order)
//				$cur_sort_order = '';
							
			$sort_link = new Link();
			$sort_link->clearVars('tab');
			$sort_link->addVar($ovar, $new_sort_order);
			echo '<a href="' . $sort_link->getUrl() . '" class="content_link">';
			echo $h['title'];
			if ($cur_sort_order == 'ASC')
				echo '<img src="images/lcm/asc_desc_arrow.gif" alt="" border="0" />';
			else if ($cur_sort_order == 'DESC')
				echo '<img src="images/lcm/desc_asc_arrow.gif" alt="" border="0" />';
			else
				echo '<img src="images/lcm/sort_arrow.gif" alt="" border="0" />';

			echo "</a>";
		} else {
			echo $h['title'];
		}

		if (isset($h['more']) && $h['more']) {
			$more_name = 'more_' . $h['more'];

			if (isset($_REQUEST[$more_name]) && $_REQUEST[$more_name]) {
				$more_link = new Link();
				$more_link->addVar($more_name, 0);

				echo '&nbsp;<span class="noprint">';
				echo '<a title="' . _T('fu_button_desc_less') . '" href="' . $more_link->getUrl() . '" class="content_link">';
				echo '<img src="images/spip/moins.gif" alt="" border="0" />';
				echo '</a></span>';
			} else {
				$more_link = new Link();
				$more_link->addVar($more_name, 1);

				echo '&nbsp;<span class="noprint">';
				echo '<a title="' . _T('fu_button_desc_more') . '" href="' . $more_link->getUrl() . '" class="content_link">';
				echo '<img src="images/spip/plus.gif" alt="" border="0" />';
				echo '</a></span>';
			}
		}
		
		echo "</th>";
	}

	echo "</tr>\n";
}

function show_list_end($current_pos = 0, $number_of_rows = 0, $allow_show_all = false, $prefix = '') {
	global $prefs;

	$prefix_var = ($prefix ? $prefix . '_' : '');
	echo "</table>\n";

	//
	// Navigation for previous/next screens
	//
	$list_pages = ceil($number_of_rows / $prefs['page_rows']);

	if (! $list_pages) {
		echo "<!-- list_pages == 0 -->\n";
		return;
	}

	$link = new Link();
	$pos = $link->getVar($prefix_var . 'list_pos');
	$link->delVar($prefix_var . 'list_pos');

	// If we are showing "All" items, do not show navigation
	if ($pos == 'all')
		return '';

	echo "<table border='0' align='center' width='99%' class='page_numbers'>\n";
	echo '<tr><td align="left" width="15%">';

	// Previous page
	if ($current_pos > 0) {
		if ($current_pos > $prefs['page_rows'])
			$link->addVar($prefix_var . 'list_pos', $current_pos - $prefs['page_rows']);

		echo '<a href="' . $link->getUrl($prefix) . '" class="content_link">'
			. "&lt; " . _T('listnav_link_previous')
			. '</a> ';
	}

	echo "</td>\n";
	echo '<td align="center" width="70%">';

	// Page numbers with direct links
	if ($list_pages > 1) {
		echo _T('listnav_link_gotopage') . ' ';
		$elipsis = false;
		for ($i = 0; $i < $list_pages; $i++) {
			if ($i == floor($current_pos / $prefs['page_rows'])) {
				echo '[' . ($i+1) . '] ';
			} else {
				$current_pos_val = ($i * $prefs['page_rows']);
				$link = new Link();
				$link->delVar($prefix_var . 'list_pos');

				if ($current_pos_val > 0)
					$link->addVar($prefix_var . 'list_pos', $current_pos_val);
				if ($list_pages < 13 ) 
					{
					echo '<a href="' . $link->getUrl($prefix) . '" class="content_link">' . ($i+1) . '</a> ';
					}
				else
					{
					if ($i < 4)
						{
						echo '<a href="' . $link->getUrl($prefix) . '" class="content_link">' . ($i+1) . '</a> ';
						$elipsis = false;
						}
					elseif ($i > $list_pages-5)
						{
						echo '<a href="' . $link->getUrl($prefix) . '" class="content_link">' . ($i+1) . '</a> ';
						$elipsis = false;
						}
					elseif (($i < (floor($current_pos / $prefs['page_rows']))+3) AND ($i > (floor($current_pos / $prefs['page_rows']))-3))
						{
						echo '<a href="' . $link->getUrl($prefix) . '" class="content_link">' . ($i+1) . '</a> ';
						$elipsis = false;
						}
					else
						{
						if (!$elipsis)
							{
							echo "... ";
							$elipsis = true;
							}
							
						}
					}

			}
		}

		if ($allow_show_all) {
			$link->delVar($prefix_var . 'list_pos');
			$link->addVar($prefix_var . 'list_pos', 'all');
			echo '<a href="' . $link->getUrl($prefix) . '" class="content_link">' . _T('listnav_link_show_all') . '</a>';
		}
	}

	echo "</td>\n";
	echo "<td align='right' width='15%'>";

	// Next page
	$next_pos = $current_pos + $prefs['page_rows'];
	if ($next_pos < $number_of_rows) {
		$current_pos_val = $next_pos;
		$link = new Link();
		$link->addVar($prefix_var . 'list_pos', $current_pos_val);

		echo '<a href="' . $link->getUrl($prefix) . '" class="content_link">'
			. _T('listnav_link_next') . " &gt;"
			. '</a>';
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}

// see listclients.php for example
function show_listclient_start() {
	$headers = array();
	$headers[0]['title'] = "#";
	$headers[0]['order'] = 'order_id';
	$headers[0]['default'] = '';

	$headers[1]['title'] = _Th('person_input_name');
	$headers[1]['order']  = 'order_name_first';
	$headers[1]['default'] = 'ASC';
//	$headers[1]['width'] = "60%";
// MATT WAS HERE. ADDING "H O NUMBER", "NASS NUMBER" AND "DOB" TO COLUMN HEADINGS FOR CLIENT		
	$headers[2]['title'] = "Home Office number";
	$headers[3]['title'] = "Nass number";
	$headers[4]['title'] = "Date of birth";

	show_list_start($headers);
}


function show_listadd_start() {
	$headers = array();
	$headers[0]['title'] = "#";
	$headers[0]['order'] = 'order_id';
	$headers[0]['default'] = '';
	
	$headers[1]['title'] = 'Name';
	
	$headers[2]['title'] = 'Details';

	$headers[3]['title'] = "Date Updated";

	show_list_start($headers);
}

function show_listadd_end($current_pos = 0, $number_of_rows = 0) {
	show_list_end($current_pos, $number_of_rows);
}

function show_listclient_end($current_pos = 0, $number_of_rows = 0) {
	show_list_end($current_pos, $number_of_rows);
}

// see listcases.php for example
function show_listcase_start($listtype=0,$admin=false) {
	// [ML] $case_court_archive = read_meta('case_court_archive');

	$cpt = 0;
	$headers = array();

//	$headers[$cpt]['title'] = '#'; // TRAD
//	$headers[$cpt]['order'] = 'no_order';
//	$cpt++;
	if ($listtype>0)
		{
		$headers[$cpt]['title'] = "Client";
		$headers[$cpt]['order'] = 'no_order';
	//	$headers[$cpt]['order'] = 'case_details';
		$cpt++;
		}	
	
	if (($listtype==12)||($listtype==1)||($listtype==2))
		{
		$headers[$cpt]['title'] = "Priority";
		$headers[$cpt]['order'] = 'accom_order';
		$headers[$cpt]['default'] = 'DESC';
		$cpt++;
		}
	if (($listtype==1)||($listtype==2))
		{
		$headers[$cpt]['title'] = "Suitability";
		$cpt++;
		}
	if (($listtype==1)||($listtype==2)||($listtype==21)||($listtype==22)||($listtype==41))
		{
		$headers[$cpt]['title'] = "Panel Score";
		$headers[$cpt]['order'] = 'pannel_order';
		$cpt++;
		}

	if ($listtype==21)
		{
		$headers[$cpt]['title'] = "Status";
		$headers[$cpt]['order'] = 'status_order';
		$cpt++;
		}

	if ($listtype==0)
		{
		$headers[$cpt]['title'] = "Type";
		$headers[$cpt]['order'] = 'no_order';
		$cpt++;

		// Orders without much logic, but better than nothing
		// (statuses are not with numeric title (for good ordering))
		$headers[$cpt]['title'] = "Status";
		$headers[$cpt]['order'] = 'no_order';
	//	$headers[$cpt]['order'] = 'status_order';
		$cpt++;

		$headers[$cpt]['title'] = 'Updated on';
		$headers[$cpt]['order'] = 'no_order';
//		$headers[$cpt]['order'] = 'update_order';
//		$headers[$cpt]['default'] = 'DESC';
		$cpt++;
		
		$headers[$cpt]['title'] = 'Next Event';
		$headers[$cpt]['order'] = 'no_order';
		$cpt++;
		}
	else
		{
		if (($listtype ==1)||($listtype ==21)||($listtype ==22)|($listtype==41))
			{
			$headers[$cpt]['title'] = 'Submitted on';
			$headers[$cpt]['order'] = 'fudate';
			}
		elseif ($listtype ==42)
			{
			$headers[$cpt]['title'] = 'Befriended on';
			$headers[$cpt]['order'] = 'fudate';
			}	
		elseif ($listtype ==2 || $listtype==12)
			{
			$headers[$cpt]['title'] = 'Added on';
			$headers[$cpt]['order'] = 'fudate';
			}	
		elseif (($listtype ==23)||($listtype==3))
			{
			$headers[$cpt]['title'] = 'Supported on';
			$headers[$cpt]['order'] = 'fudate';
			$cpt++;
			$headers[$cpt]['title'] = 'Amount';
			$headers[$cpt]['order'] = 'amount_order';
//			$headers[$cpt]['default'] = 'ASC';
			}
		elseif (($listtype ==24)||($listtype ==4))
			{		
			$headers[$cpt]['title'] = 'Submitted on';
			$headers[$cpt]['order'] = 'fudate';
			$cpt++;
			$headers[$cpt]['title'] = 'Status';
			$headers[$cpt]['order'] = 'no_order';
			}
		elseif ($listtype ==5)
			{
			$headers[$cpt]['title'] = 'Terminated on';
			$headers[$cpt]['order'] = 'fudate';
			$cpt++;
			$headers[$cpt]['title'] = 'Details';
			}
		elseif ($listtype ==25)
			{
			$headers[$cpt]['title'] = 'Supported on';
			$headers[$cpt]['order'] = 'fudate';
			}
		elseif (($listtype ==26)||($listtype==6))
			{
			$headers[$cpt]['title'] = 'Rejected on';
			$headers[$cpt]['order'] = 'fudate';
			}
//		$headers[$cpt]['order'] = 'case_order';
		$cpt++;
		}
	if ($listtype ==23)
		{
		$headers[$cpt]['title'] = 'Supported By';
			$headers[$cpt]['order'] = 'by_order';
		$cpt++;
		}
	if (($listtype != 12&&$listtype!=23)&&($admin))
		{
		$headers[$cpt]['title'] = 'Options';
		$headers[$cpt]['order'] = 'no_order';
		$cpt++;
		}

	show_list_start($headers);
}

function show_listcase_item($item, $cpt, $find_case_string = '', $url = '__DEFAULT__', $url_extra = '',$listtype,$admin=false) {
	include_lcm('inc_acc');

	$css = ($cpt %2 ? "dark" : "light");
	$css2= ($item['stage']=='z'?'gcont_':'cont_');

	if ($url == '__DEFAULT__')
		$url = 'case_det.php?case=' . $item['id_case'];

	echo "<tr>\n";
	// Case ID
//	echo "<td class='tbl_cont_" . $css . "'>";
//	echo highlight_matches($item['id_case'],$find_case_string);
//	echo "</td>\n";
	if ($listtype>0&&$item['id_client'])
		{// FOR ALL LISTS EXCEPT ONE ON CLIENT'S PAGE, SHOW CLIENT ID NO.
		// Client
		echo "<td class='tbl_".$css2 . $css . "'>";
		echo "<a href='client_det.php?client=".$item['id_client']."' class='content_link'>" . highlight_matches(clean_output($item['name_first'].' '.$item['name_last']),$find_case_string)."</a>";
		if ($listtype!=12)
			{echo "<br/><br/>";}
		echo "</td>\n";
		}

	//Scores
	if ($listtype==12)
		{
		echo "<td class='tbl_".$css2 . $css . "'>";
		echo $item['accom'];
		echo "</td>";
		}
	if (($listtype==1)||($listtype==2))
		{
		echo "<td class='tbl_".$css2 . $css . "'>";
		echo $item['accom'];
		echo "</td>";
		echo "<td class='tbl_cont_" . $css . "'>";
		echo '<small>Home Stay: '.($item['check1']?'Yes':'No').'<br/>';
		echo 'Shared House: '.($item['check2']?'Yes':'No').'<br/>';
		echo 'Nightshelter: '.($item['check3']?'Yes':'No').'<br/>';
		echo "</small></td>";
		}
	if (($listtype==1)||($listtype==2)||($listtype==21)||($listtype==22)||($listtype==41))
		{
		echo "<td class='tbl_".$css2 . $css . "'>";
		echo $item['pannel'];
		echo "</td>";
		}

	if ($listtype==21)
		{
		echo "<td class='tbl_".$css2 . $css . "'>";
		switch ($item['stage'])
			{
			case 'z':
				echo 'Deferred';
				break;
			case 'submitted':
				echo 'New Referal';
				break;
			case 'submitted2':
				echo '2nd Chance';
				break;
			case 'submitted3':
				echo '3rd Chance';
				break;
			case 'submitted4':
				echo 'Last Chance';
				break;
			}

		echo "</td>";
		}
	// Type
	if ($listtype==0)
		{//FOR LISTS OF STRANDS ON CLIENT'S PAGE.
		echo "<td class='tbl_".$css2 . $css . "'>";
		echo "<a href='case_det.php?case=".$item['id_case']."' class='content_link'>".$item['type_case']."</a>";
	//	if ($ac_read) echo '<a href="' . $url . '" class="content_link" ' . $url_extra . '>';
	//	echo highlight_matches(clean_output($item['title']),$find_case_string);
	//	if (allowed($item['id_case'],'r')) echo '</a>';
		echo "</td>\n";

		// Status and Stage
		echo "<td class='tbl_".$css2 . $css . "'>";
		
		echo _T('case_status_option_'.$item['status']) . " - ";
		$kws= get_keywords_in_group_name('stage');
		foreach ($kws as $kw)
			{
			if ($kw['name']==$item['stage'])
				{
				echo $kw['title'];
				}
			}
		echo "</td>\n";
		// Date update
		echo "<td class='tbl_".$css2 . $css . "'>";
		echo format_date($item['date_update'], 'date_short');
		echo "</td>\n";
		// Date next event
		echo "<td class='tbl_".$css2 . $css . "'>";
		echo ($item['start_time']?format_date($item['start_time'], 'date_short')." ".($item['ttl']=='suprev'||$item['ttl']=='accrev'?"(Review)":"(Termination)"):"---");
		echo "</td>\n";
		}
	else
		{
		// Date creation
		echo "<td class='tbl_".$css2 . $css . "'>";
		echo format_date($item['date_start'], 'date_short');
		echo "</td>\n";
		}

	if (($listtype==4) ||($listtype==24))//all suppor\t
		{
		echo "<td class='tbl_".$css2 . $css . "'>";
		echo _T('case_status_option_'.$item['status']) . " - ";
		$kws= get_keywords_in_group_name('stage');
		foreach ($kws as $kw)
			{
			if ($kw['name']==$item['stage'])
				{
				echo $kw['title'];
				}
			}
		echo "</td>\n";
		}
	if ($listtype==5)//terminatd (show reason)
		{
		echo "<td class='tbl_".$css2 . $css . "'>";
		echo get_fu_description($item, true);
		echo "</td>";
		}

	switch ($listtype) 
		{
		case '1':
			$ref="ref=listaccom.php?tab=new";
			break;
		case '2':
			$ref="ref=listaccom.php?tab=wait";
			break;
		case '3':
			$ref="ref=listaccom.php?tab=supported";
			break;
		case '21':
			$ref="ref=listpannel.php?tab=new";
			break;
		case '22':
			$ref="ref=listpannel.php?tab=old";
			break;
		case '41':
			$ref="ref=befriender.php?tab=new";
			break;
		case '42':
			$ref="ref=befriender.php?tab=bef";
			break;
		case '23':
			$ref="ref=listpannel.php?tab=supported";
			break;
		case '24':
			$ref="ref=listpannel.php?tab=all";
			break;
		default:
		}

	if ($listtype==23 || $listtype==3)
		{
		echo "<td class='tbl_".$css2 . $css . "'>";
		echo "£ ".$item['amount'].($item['legal_reason']=='yes'?' & Bus Pass':'');
		echo "</td>";
		}	
	if ($listtype==23)
		{
		echo "<td class='tbl_".$css2 . $css . "'>";
		echo ($item['type_case']=='Support'?'Panel':$item['type_case']);
		echo "</td>\n";
		}


	// actions
	if ((($listtype != '12')&&$listtype!='23')&&($admin))
		{
		echo "<td class='tbl_".$css2 . $css . "'>";
		if ($item['status']=='open')
			{
			if ($item['type_case']=='Befriender')
				{	
				if ($item['stage']=='submitted')
					{
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=befriender&type=stage_change&stage=rejected&'.$ref.'" class="edit_lnk click_btn">Reject</a>';
					}
				if ($item['stage']=='bef')
					{
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=befriender&type=stage_change&stage=terminated&'.$ref.'" class="edit_lnk click_btn">Terminate</a>';
					}
				}
			if ($item['type_case']=='Support')
				{	
				if ($item['stage']=='submitted')
					{
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=supported&'.$ref.'" class="edit_lnk click_btn">Support</a>';
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=submitted2&'.$ref.'" class="edit_lnk click_btn">Defer</a>';
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=rejected&'.$ref.'" class="edit_lnk click_btn">Reject</a>';
					}
				if ($item['stage']=='submitted2')
					{
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=supported&'.$ref.'" class="edit_lnk click_btn">Support</a>';
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=submitted3&'.$ref.'" class="edit_lnk click_btn">Defer</a>';
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=rejected&'.$ref.'" class="edit_lnk click_btn">Reject</a>';
					}
				if ($item['stage']=='submitted3')
					{
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=supported&'.$ref.'" class="edit_lnk click_btn">Support</a>';
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=submitted4&'.$ref.'" class="edit_lnk click_btn">Defer</a>';
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=rejected&'.$ref.'" class="edit_lnk click_btn">Reject</a>';
					}
				if ($item['stage']=='submitted4' || $item['stage']=='z')
					{
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=supported&'.$ref.'" class="edit_lnk click_btn">Support</a>';
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=rejected&'.$ref.'" class="edit_lnk click_btn">Reject</a>';
					}
//				elseif ($item['stage']=='submitted supported')
//					{
//					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=supported&'.$ref.'" class="edit_lnk click_btn">Support</a>';
//					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=submitted supported&'.$ref.'" class="edit_lnk click_btn">Temporarily Support</a>';
//					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=submitted unsupported&'.$ref.'" class="edit_lnk click_btn">End Temporary Support</a>';
//					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=rejected&'.$ref.'" class="edit_lnk click_btn">Reject</a>';
//					}
				elseif ($item['stage']=='supported')
					{
//					echo ($item['ttl']=='suprev'||$item['ttl']=='accrev'?	
//						'<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=review&'.$ref.'" class="edit_lnk click_btn">Review Now</a>'
//						:
//						'<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=terminated&'.$ref.'" class="edit_lnk click_btn">Terminate Now</a>'
//						);
					}
				elseif ($item['stage']=='supported_notice')
					{
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=support&type=stage_change&stage=terminated&'.$ref.'" class="edit_lnk click_btn">Terminate</a>';
					}
				}
			elseif ($item['type_case']=='Accomidation')
				{
				if ($item['stage']=='submitted')
					{
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=accomidation&type=stage_change&stage=waiting list&'.$ref.'" class="edit_lnk click_btn">Add to waiting list</a>';
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=accomidation&type=stage_change&stage=rejected&'.$ref.'" class="edit_lnk click_btn">Reject</a>';
					}
				elseif ($item['stage']=='waiting list')
					{
	//				echo '<a href="listrooms.php?case='.$item['id_case'].'&action=make_prov&'.$ref.'" class="content_link">Find Room</a>';
					echo '<a href="edit_fu.php?case='.$item['id_case'].'&ctype=accomidation&type=stage_change&stage=rejected&'.$ref.'" class="edit_lnk click_btn">Reject</a>';
					}
				elseif ($item['stage']=='supported')
					{
					echo ($item['ttl']=='suprev'||$item['ttl']=='accrev'?	
						'<a href="edit_fu.php?case='.$item['id_case'].'&ctype=accomidation&type=review&'.$ref.'" class="edit_lnk click_btn">Review Now</a>'
						:
						'<a href="edit_fu.php?case='.$item['id_case'].'&ctype=accomidation&type=stage_change&stage=terminated&'.$ref.'" class="edit_lnk click_btn">Terminate Now</a>'
						);
					}
				}
			}
		}
	echo "</td>\n";
	echo "</tr>\n";
}

function show_listcase_end($current_pos = 0, $number_of_rows = 0) {
	show_list_end($current_pos, $number_of_rows);
}

// see listcases.php for example
function show_listfu_start($screen = 'general', $show_more_desc = true) {
	global $prefs;
	echo "<table class='tbl_usr_dtl' width=99%>";
	return;

	$cpt = 0;
	$headers = array();
//	if ($screen != 'case') {
//		$headers[$cpt]['title'] = "#";
//		$headers[$cpt]['order'] = 'no_order';
//		$cpt++;

//		$headers[$cpt]['title'] = "c#";
//		$headers[$cpt]['order'] = 'no_order';
//		$cpt++;
//	}

//MATT WAS HERE: REMOVING THE "TIME" TITLE FROM THE FOLLOWUPS LISTINGS.
	$headers[$cpt]['title'] = 'Date';//_Th('time_input_date_start');
	$headers[$cpt]['order'] = 'fu_order';
	$headers[$cpt]['default'] = 'ASC';
	$cpt++;

//	$headers[$cpt]['title'] = (($prefs['time_intervals'] == 'absolute') ? _Th('time_input_date_end') : _Th('time_input_length'));
//	$headers[$cpt]['order'] = 'no_order';
//	$cpt++;

	if ($screen != 'author') {
		$headers[$cpt]['title'] = _Th('case_input_author');
		$headers[$cpt]['order'] = 'no_order';
		$cpt++;
	}

	$headers[$cpt]['title'] = _Th('fu_input_type');
	$headers[$cpt]['order'] = 'no_order';
	$cpt++;
	
	$headers[$cpt]['title'] = _Th('fu_input_description');
	$headers[$cpt]['order'] = 'no_order';
// MATT WAS HERE: ALLWAYS SHOW THE LITTLE "+" ICON TO EXPAND THE FOLLOW-UPS TEXT
//	if ($show_more_desc)
		$headers[$cpt]['more'] = 'fu_desc'; // will create var ?more_fu_desc=1

	$cpt++;

	show_list_start($headers);
}

// function can be used to find case work on followup_id.
function show_listfu_item($item, $cpt, $screen = 'general') {
	global $prefs;
        // If current user is author of this follow-up, then create a link for them to edit it
        $editable = $GLOBALS['author_session']['name_first'] === $item['name_first']
                && $GLOBALS['author_session']['name_last'] === $item['name_last'];
	$desc = get_fu_description($item, $cut_fu);
	$fu_id=$item['id_followup'];
	echo 	"<tr><td class='tbl_cont_".($cpt%2?'dark':'light')."'>".
		'<b><big>';
	if ($editable) {
		echo "<a class='edit_casework' href='edit_fu.php?followup=$fu_id'>";
	}
	echo 
		($item['type_case']=='Default'?"":$item['type_case'].": ").
		_Tkw('followups', $item['type']);
	if ($editable) {
		echo '</a>';
	}
	echo
		 '</big></b>'.
		' on the <b>'.
		format_date($item['date_start'], 'short').
		'</b> by '.
		 clean_output($item['name_first']." ".$item['name_last']).

		// display the outcome amount
		($item['outcome_amount']?"<p>Paid: £".$item['outcome_amount']."</p>":"");
		
	// add "Bus Pass Given" if one was given
	if ($item['bus_pass_given'] === '1') {
		echo "<p>Bus Pass Given</p>";
	}

		// display description (notes)
	echo ($desc=="No description."?"":"<p>".$desc."</p>"). "</td></tr>";


	return;
	echo "<tr>\n";

	// Id case
//	if ($screen == 'case')
//		echo '<td valign="top"><abbr title="' . $item['title'] . '">' . $item['id_case'] . '</abbr></td>';
//	else
//		{
//		echo '<td valign="top"><a href="case_det.php?case=' . $item['id_case'] . '">'.$item['id_case'].'</a>';
//		echo '/' . $item['id_followup'] . '</td>';	
//		}
//	// Start date
	echo '<td valign="top">' . format_date($item['date_start'], 'short') . '</td>';
//MATT WAS HERE REMOVING "TIME SPENT" FROM THE LIST OF FOLLOWUPS.					
	// Time
//	echo '<td valign="top">';
//	$fu_date_end = vider_date($item['date_end']);
//	if ($prefs['time_intervals'] == 'absolute') {
//		if ($fu_date_end) echo format_date($item['date_end'],'short');
//	} else {
//		$fu_time = ($fu_date_end ? strtotime($item['date_end']) - strtotime($item['date_start']) : 0);
//		echo format_time_interval($fu_time,($prefs['time_intervals_notation'] == 'hours_only'));
//	}
//	echo '</td>';

	// Author initials
	if ($screen != 'author')
		echo '<td valign="top">' . get_person_initials($item) . '</td>';

	// Type
	echo '<td valign="top">' . _Tkw('followups', $item['type']) . '</td>';

	// Description
	$cut_fu = (isset($_REQUEST['more_fu_desc']) && $_REQUEST['more_fu_desc'] ? false : true);
	$short_description = get_fu_description($item, $cut_fu);

	if ($item['hidden'] == 'Y')
		$short_description .= ' <img src="images/jimmac/stock_trash-16.png" '
			. 'height="16" width="16" border="0" '
			. 'title="' . _T('fu_info_is_deleted') . '" '
			. 'alt="' . _T('fu_info_is_deleted') . '" />';

//	if ($item['outcome']|| $item['outcome_amount'])
//		{
	if ($item['outcome'])
		{
		$zot = get_kw_from_id($item['outcome']);
		$short_description .= '<br/> Outcome: '. $zot['title'];
		}
	if (!($item['outcome_amount']==0))
		{
		$short_description .= '<br/> Amount: �'.$item['outcome_amount'];
		}
//		}
	echo '<td valign="top">';
	echo '<a href="fu_det.php?followup=' . $item['id_followup'] . '" class="content_link">' . $short_description . '</a>';
	echo '</td>';

	echo "</tr>\n";
}

//MATT WAS HERE. SPECIAL VERSION OF "SHOW_LISTFU_ITEM" FORMATTED FOR SIMPLE PRINTER-READY PAGE.
function show_matt_listfu_item($item, $cpt, $screen = 'general') {
	global $prefs;
	
	echo '<hr/><p><b>';
	echo format_date($item['date_start'], 'short');
	echo '</b> Work: <b>'._Tkw('followups', $item['type']);
	echo '</b> By: <b>'.get_person_initials($item);
	echo '</b></p>';
	echo get_fu_description($item, false);

//	$fu_date_end = vider_date($item['date_end']);
//	if ($prefs['time_intervals'] == 'absolute') {
//		if ($fu_date_end) echo format_date($item['date_end'],'short');
//	} else {
//		$fu_time = ($fu_date_end ? strtotime($item['date_end']) - strtotime($item['date_start']) : 0);
//		echo format_time_interval($fu_time,($prefs['time_intervals_notation'] == 'hours_only'));
//	}

}

function show_find_box($type, $string, $dest = '', $layout = 'normal', $tab='') {
	static $find_box_counter = 0; // there may be more than one search box for a given type, in same page

	if ($type == 'client' && read_meta('client_hide_all') == 'yes')
		return;

	if ($type == 'org' && read_meta('org_hide_all') == 'yes')
		return;

	switch ($type) {
		case 'client':
		case 'org':
		case 'author':
		case 'rep':
		case 'exp':
		case 'add':
			$action = 'list' . $type . 's.php';
			break;
		case 'case':
			$action = 'case_det.php';
			break;
		case 'accom':
			$action = 'listaccom.php';
			break;
		default:
			lcm_panic("invalid type: $type");
	}

	if ($dest) {
		if ($dest == '__self__') {
			$link_dest = new Link();
			$link_dest->delVar('find_' . $type . '_string');
			$link_dest->delVar('submit');
			echo $link_dest->getForm('get', '', '', 'search_form');
		} else {
			$action = $dest;
		}
	} else {
		echo '<form name="frm_find_' . $type . '" class="search_form" action="' . $action . '" method="get">' . "\n";
	}

	echo '<label for="find_box' . $find_box_counter . '">';
	echo _T('input_search_' . $type) . "&nbsp;";
	echo "</label>\n";

	if ($layout == 'narrow')
		echo "<br />\n";
	
	echo '<input type="text" id="find_box' . $find_box_counter . '" name="find_' . $type . '_string" size="10" class="search_form_txt" value="' . clean_output($string) . '" />';
	echo '&nbsp;<input type="submit" name="submit" value="' . _T('button_search') . '" class="search_form_btn" />' . "\n";
	if ($tab)
		{
		echo '<input type="hidden" name="tab" value="'.$tab.'" />';
		}
	echo "</form>\n";

	$find_box_counter++;
}

function show_context_start() {
	echo "<ul style=\"padding-left: 0.5em; padding-top: 0.2; padding-bottom: 0.2; font-size: 12px;\">\n";
}

function show_context_item($string) {
	echo '<li style="list-style-type: none;">' . $string . "</li>\n";
}

function show_context_case_title($id_case, $link_tab = '') {
	if (! (is_numeric($id_case) && $id_case > 0)) {
		lcm_log("Warning: show_context_case_title: id_case not a number > 0: $id_case");
		return;
	}

	// Send back to follow-up tab (ex: from 'fu details')
	$link_tab = ($link_tab ? '&amp;tab=' . $link_tab : '');

	$query = "SELECT title FROM lcm_case WHERE id_case = $id_case";
	$result = lcm_query($query);

	while ($row = lcm_fetch_array($result))  // should be only once
		echo '<li style="list-style-type: none;">' 
			. _Ti('fu_input_for_case')
			. "<a href='case_det.php?case=$id_case$link_tab' class='content_link'>" . $row['title'] . "</a>"
			. "</li>\n";
}

function show_context_case_stage($id_case, $id_followup = 0) {
	if (! (is_numeric($id_case) && $id_case > 0)) {
		lcm_log("Warning: show_context_case_stage, id_case not a number > 0: $id_case");
		return;
	}

	if (! (is_numeric($id_followup))) {
		lcm_log("Warning: show_context_case_stage, id_followup not a number >= 0: $id_followup");
		return;
	}

	if ($id_followup)
		$query = "SELECT case_stage as stage FROM lcm_followup WHERE id_followup = $id_followup";
	else
		$query = "SELECT stage FROM lcm_case WHERE id_case = $id_case";

	$result = lcm_query($query);

	while ($row = lcm_fetch_array($result))  // should be only once
		echo '<li style="list-style-type: none;">' 
			. _Ti('case_input_stage')
			. _Tkw('stage', $row['stage'])
			. "</li>\n";
}

function show_context_case_involving($id_case) {
	if (! (is_numeric($id_case) && $id_case > 0)) {
		lcm_log("Warning: show_context_case_involving, id_case not a number > 0: $id_case");
		return;
	}

	$query = "SELECT cl.id_client, name_first, name_middle, name_last
				FROM lcm_case_client_org as cco, lcm_client as cl
				WHERE cco.id_case = $id_case
				  AND cco.id_client = cl.id_client";
	
	$result = lcm_query($query);
	$numrows = lcm_num_rows($result);

	$current = 0;
	$all_clients = array();
	
	while ($all_clients[] = lcm_fetch_array($result));
	
	$query = "SELECT org.name, cco.id_client, org.id_org
				FROM lcm_case_client_org as cco, lcm_org as org
				WHERE cco.id_case = $id_case
				  AND cco.id_org = org.id_org";
	
	$result = lcm_query($query);
	$numrows += lcm_num_rows($result);
	
	// TODO: It would be nice to have the name of the contact for that
	// organisation, if any, but then again, not the end of the world.
	// (altough I we make a library of common functions, it will defenitely
	// be a good thing to have)
	while ($all_clients[] = lcm_fetch_array($result));
	
	if ($numrows > 0)
		echo '<li style="list-style-type: none;">' . _T('fu_input_involving_clients') . " ";
	
	foreach ($all_clients as $client) {
		if ($client['id_client']) {
			echo '<a href="client_det.php?client=' . $client['id_client'] . '" class="content_link">'
				. get_person_name($client)
				. '</a>';
	
			if (++$current < $numrows)
				echo ", ";
		} else if ($client['id_org']) {
			echo '<a href="org_det.php?org=' . $client['id_org'] . '" class="content_link">'
				. $client['name']
				. '</a>';
	
			if (++$current < $numrows)
				echo ", ";
		}
	
	}
	
	if ($numrows > 0)
		echo "</li>\n";
}

function show_context_stage($id_case, $id_stage) {
	if (! (is_numeric($id_case) && $id_case > 0)) {
		lcm_log("Warning: show_context_stage, id_case not a number > 0: $id_case");
		return;
	}

	if (! (is_numeric($id_stage) && $id_stage > 0)) {
		lcm_log("Warning: show_context_stage, id_stage not a number > 0: $id_stage");
		return;
	}

	include_lcm('inc_keywords');
	$kws = get_keywords_applied_to('stage', $id_case, $id_stage);

	foreach($kws as $kw) {
		echo '<li style="list-style-type: none;">';

		if ($kw['value']) {
			echo _Ti(remove_number_prefix($kw['title']));
			echo $kw['value'];
		} else {
			echo _Ti(remove_number_prefix($kw['kwg_title'])) . _T(remove_number_prefix($kw['title']));
		}

		echo "</li>\n";
	}
}

function show_context_end() {
	echo "</ul>\n";
}

function show_page_subtitle($subtitle, $help_code = '', $help_target = '') {
	echo '<div class="prefs_column_menu_head">';
//MATT WAS HERE, REMOVING HELP QUESTION MARKS
//	if ($help_code)
//		echo '<div class="help_icon">' . lcm_help($help_code, $help_target) . "</div>";
	
	echo $subtitle;
	echo "</div>\n";
}

function show_attachments_list($type, $id_type, $id_author = 0) {
	if (! ($type == 'case' || $type == 'client' || $type == 'org')) 
		lcm_panic("unknown type -" . $type . "-");

	$q = "SELECT * 
			FROM lcm_" . $type . "_attachment 
			WHERE content IS NOT NULL ";
	
	if ($id_type)
		$q .= " AND id_" . $type . " = " . intval($id_type);
	
	if ($id_author)
		$q .= " AND id_author = " . intval($id_author);

	$result = lcm_query($q);
	$i = lcm_num_rows($result);

	if ($i > 0) {
		echo '<table border="0" align="center" class="tbl_usr_dtl" width="99%">' . "\n";
		echo "<tr>\n";

		if ($id_author)
			echo '<th class="heading" width="1%">' . _Th($type . '_input_id') . "</th>\n";

		echo '<th class="heading">' . _Th('file_input_type') . "</th>\n";
		echo '<th class="heading">' . _Th('file_input_description') . "</th>\n";
		echo '<th class="heading">' . Date . "</th>\n";
		echo '<th class="heading">' . _Th('file_input_size') . "</th>\n";
		echo '<th class="heading">' . "</th>\n";
		echo "</tr>\n";

		for ($i=0 ; $row = lcm_fetch_array($result) ; $i++) {
			echo "<tr>\n";

			if ($id_author) {
				echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '" align="left">';
				
				echo '<a href="' . $type . '_det.php?' . $type . '=' . $row['id_' . $type] . '" class="content_link">'
					. $row['id_' . $type] 
					. '</a>';

				echo "</td>\n";
			}

			// Mimetype
			// [ML] We were using the mimetype sent by the browser, but it
			// ends up being rather useless, since MSIE and Firefox don't agree on
			// the mimetypes.. ex: .jpg = image/jpeg (FFx), but under MSIE is image/pjeg
			// So may as well just use the extention of the file, even if not reliable.
			echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '" align="left">';
			echo '<a title="' . $row['type'] . '" '
				. 'href="view_file.php?type=' . $type . '&amp;file_id=' .  $row['id_attachment'] . '">';

			if (preg_match("/\.([a-zA-Z0-9]+)$/", $row['filename'], $regs)
				&& is_file("images/mimetypes/" . strtolower($regs[1]) . ".png"))
			{
					echo '<img src="images/mimetypes/' . $regs[1] . '.png" border="0" alt="' . $row['type'] . '" />';
			} else {
				echo '<img src="images/mimetypes/unknown.png" border="0" alt="' . $row['type'] . '" />';
			}

			echo '</a>';
			echo '</td>';

			// File name (or description, if any)
			echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '">'
				. '<a title="' . $row['filename'] . '" '
				. 'href="view_file.php?type=' . $type . '&amp;file_id=' . $row['id_attachment'] . '" class="content_link">';
			echo (trim($row['description']) ? $row['description'] : $row['filename']);
			echo '</a></td>';

			//MATT WAS HERE, ADDED DATE FIELD TO ATTACHMENT LIST
			//Date uploaded
			echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '">' . $row['date_attached'] . '</td>';

			// Size
			echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '">' . size_in_bytes($row['size']) . '</td>';

			// Delete icon
			echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '">';

			if ( ($GLOBALS['author_session']['status'] == 'admin') || (($row['id_author'] == $GLOBALS['author_session']['id_author']) && ($type == 'case' ? allowed($id_type,'e') : true)) )
			{
				echo '<label for="id_rem_file' . $row['id_attachment'] . '">';
				echo '<img src="images/jimmac/stock_trash-16.png" width="16" height="16" '
					. 'alt="' . _T('file_info_delete') . '" title="' .  _T('file_info_delete') . '" />';
				echo '</label>&nbsp;';
				echo '<input type="checkbox" onclick="lcm_show(\'btn_delete\')" '
					. 'id="id_rem_file' . $row['id_attachment'] . '" name="rem_file[]" '
					. 'value="' . $row['id_attachment'] . '" />';
			}

			echo '</td>';
			echo "</tr>\n";
		}

		echo "</table>\n";

		echo '<p align="right">';
		echo '<input type="submit" name="submit" id="btn_delete" value="' . _T('button_delete') . '" class="search_form_btn" />';
		echo "</p>\n";
	} else {
		echo '<p class="normal_text">' . _T('file_info_emptylist') . "</p>\n";
	}
}

function show_author_attachments($id_author) {

	// List attachments of every type
	// TODO: if meta for hide_org is active, don't show them
//	foreach( array('case','client','org') as $type ) {
	//MATT WAS HERE, REMOVING CLIENT AND ORG LIST OF ATTACHEMNTS
	foreach( array('case') as $type ) {
//		show_page_subtitle(_T('menu_main_' . $type . 's'), 'tools_documents');
		show_attachments_list($type, 0, $id_author);
	}
}

function show_attachments_upload($type, $id_type, $filename='', $description='') {
	if (! ($type == 'case' || $type == 'client' || $type == 'org')) 
		lcm_panic("unknown type -" . $type . "-");

	echo '<div class="prefs_column_menu_head">' . _T('generic_subtitle_document_add') . "</div>\n";

//	echo '<form enctype="multipart/form-data" action="attach_file.php" method="post">' . "\n";
//	echo '<input type="hidden" name="' . $type . '" value="' . $id_type . '" />' . "\n";
	echo '<input type="hidden" name="MAX_FILE_SIZE" value="' . $GLOBALS['max_file_upload_size'] . '" />' . "\n";

	echo '<strong>' . f_err_star('file') . _Ti('file_input_name') . "</strong><br />";
	echo '<input type="file" name="filename" size="40" value="' . $filename . '" />' . "<br />\n";

	echo '<strong>' . _Ti('file_input_description') . "</strong><br />\n";
	echo '<input type="text" name="description" class="search_form_txt" value="' . $description . '" />&nbsp;' . "\n";
//	echo '<input type="submit" name="submit" value="' . _T('button_validate') . '" class="search_form_btn" />' . "\n";
//	echo "</form>\n";
}

function show_navmenu_item($dest, $name) {
	echo '<li><a href="' . $dest . '" class="main_nav_btn" '
		. 'title="' . htmlspecialchars(_T('menu_' . $name . '_tooltip')) . '">'
		. _T('menu_' . $name) 
		. "</a></li>\n";
}

// Returns the author's name and a link to its details page.
function get_author_link($item) {
	if (! is_array($item)) {
		lcm_log("Warning: show_author_link() was not given an array");
		return;
	}

	return '<a class="content_link" '
		. 'href="author_det.php?author=' . $item['id_author'] . '" '
		. 'title="' . _T('case_tooltip_view_author_details', array('author' => get_person_name($item))) . '">'
		. get_person_name($item)
		. "</a>";
}

function get_delete_box($id, $arrname, $text) {
	$html  = '<label for="' . $id . '">';
	$html .= '<img src="images/jimmac/stock_trash-16.png" width="16" height="16" '
			. 'alt="' . $text . '" title="' . $text . '" />';
	$html .= '</label>&nbsp;';
	$html .= '<input type="checkbox" onclick="lcm_show(\'btn_delete\')" '
			. ' id="' . $id . '" name="' . $arrname . '[]" value="' . $id . '" />';
// MATT WAS HERE REMOVE DELTE BUTTON
	return $html;
}

function isChecked($expr) {
	if ($expr)
		return ' checked="checked" ';
	else
		return ' ';
}

function isSelected($expr) {
	if ($expr)
		return ' selected="selected" ';
	else
		return '';
}

function isDisabled($expr) {
	if ($expr)
		return ' disabled="disabled" ';
	else
		return '';
}

?>
