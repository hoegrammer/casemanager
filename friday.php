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

	$Id: listauthors.php,v 1.33 2006/03/21 19:01:53 mlutfy Exp $
*/

include('inc/inc.php');
include_lcm('inc_acc');
include_lcm('inc_filters');
include_lcm('inc_obj_fu');
include('notes.php');

global $author_session;


$_SESSION['form_data']=array();
$_SESSION['errors']=array();


lcm_page_start('Friday');
matt_page_start('Friday');

$admin = false;
if ($GLOBALS['author_session']['right10']==1)
	{
	$admin = true;
	}

$groups = array(
	'outstanding' => 'Post-its',
	);
show_tabs($groups, 'outstanding', $_SERVER['REQUEST_URI']);
no_tabs();

show_notes('post-friday','friday.php', $admin);


matt_page_end();
lcm_page_end();

?>
