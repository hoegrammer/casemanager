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

	$Id: edit_author.php,v 1.43 2006/08/17 14:05:53 mlutfy Exp $
*/

include('inc/inc.php');
include_lcm('inc_filters');
include_lcm('inc_contacts');

global $author_session;
$user = array(); // form data
$crib = intval(_request('id_crib'));

// Set the returning page
if (_request('ref'))
	$user['ref_edit_author'] = _request('ref');
else
	$user['ref_edit_author'] = $GLOBALS['HTTP_REFERER'];

// Find out if this is existing or new crib
$existing = ($crib > 0);

if ($existing) {
	// Check if user is permitted to edit this author's data
	if (($author_session['status'] != 'admin')){
		die("You don't have the right to edit this cribnote");
	}

	// Get author data
	$q = "SELECT * FROM lcm_crib WHERE id_crib = $crib";
	$result = lcm_query($q);
	if ($row = lcm_fetch_array($result)) {
		foreach ($row as $key => $value) {
			$note[$key] = $value;
		}
	} else {
		lcm_header("Location: listcrib.php");
		exit;
	}
} else {
	$note['id_crib'] = 0;
	$note['short'] = 'Short';
	$note['full'] = 'Long';
}

// Fetch values that caused errors to show them with the error message
if (isset($_SESSION['form_data']))
	foreach($_SESSION['form_data'] as $key => $value)
		$note[$key] = $value;

// Start the page with the proper title
if ($existing) lcm_page_start(_T('title_crib_edit'));
else lcm_page_start(_T('title_crib_new'));

echo show_all_errors($_SESSION['errors']);


?>
<form name="edit_crib" method="post" action="upd_crib.php">
	<input name="id_crib" type="hidden" id="id_crib" value="<?php echo $note['id_crib']; ?>"/>
	<input name="ref_edit_author" type="hidden" id="ref_edit_author" value="<?php 
			$ref_link = new Link($user['ref_edit_author']);
			echo $ref_link->getUrl();
		?>"/>
	<table width="99%" border="0" align="center" cellpadding="5" cellspacing="0" class="tbl_usr_dtl">
		<tr><td align="left" valign="top">Short Discription:</td>
			<td align="left" valign="top"><input name="short" type="text" class="search_form_txt" id="short" size="35" value="<?php echo clean_output($note['short']); ?>"/></td>
		</tr>
		<tr><td align="left" valign="top">Full text:</td>
			<td align="left" valign="top"><textarea name="content" class="search_form_txt" id="content" size="35"><?php echo clean_output($note['full']); ?></textarea></td>
		</tr>
		<tr><td align="left" valign="top">Case type:</td>
			<td align="left" valign="top">
			<select name="casetype">
			<?php
			$q = "SELECT id_keyword, title from lcm_keyword where id_group = 27";
			$result = lcm_query($q);
			while ($row = lcm_fetch_array($result))
				{
				echo "<option value=".$row['id_keyword'].(($note['id_keyword']==$row['id_keyword'])?" selected":"").">".$row['title']."</option>";
				}
			?>
			</select>
			<input name="visible" value="1" type="checkbox" <?php ($note['visible']>0?print 'checked':print'') ?> >Visible?</td>
		</tr>
		<tr><td colspan="2" align="center" valign="middle">
			<input name="submit" type="submit" class="search_form_btn" id="submit" value="<?php echo _T('button_validate') ?>" /></td>
		</tr>
		

	</table>
</form>

<?php

lcm_page_end();

// Reset error messages
$_SESSION['errors'] = array();
$_SESSION['form_data'] = array();
$_SESSION['usr'] = array(); // DEPRECATED 0.7.1

?>
