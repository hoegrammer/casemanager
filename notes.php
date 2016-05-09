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

function show_notes($type='post-admin',$ref='index.html',$admin=false)
	{
	$dismiss = $_GET['dismiss'];
	if ($dismiss)
		{
		lcm_query('update lcm_app set dismissed=1 where id_app='.$dismiss);
		}

	$_GET = array();

	$q = '
		SELECT ap.*, cl.name_first as clf, cl.name_last as cll, cl.id_client, au.name_first as auf, au.name_last as aul
		FROM lcm_app as ap 
		LEFT JOIN lcm_case_client_org as cco on cco.id_case = ap.id_case
		LEFT JOIN lcm_client as cl on cl.id_client = cco.id_client
		LEFT JOIN lcm_author as au on au.id_author = ap.id_author
		WHERE ap.title="'.$type.'" and dismissed = 0
		ORDER BY ap.date_creation
		';
	$result=lcm_query($q);

	if (lcm_num_rows($result))
		{
		echo "<table class='table_postit'>";
		for ($cpt = 0; ($row = lcm_fetch_array($result)); $cpt++)
			{
			if ($cpt % 3==0)
				{
				echo "<tr>";
				}
			echo "<td class='td_postit'><small>";

			print_r($author_session);
			if ($admin)	
				echo "
					<div style='padding:1px 3px 0px 0px; float:right'>
					<a href='$ref?tab=outstanding&dismiss=".$row['id_app']."' class='content_link'>Dismiss
					</a>
					</div>";
			$colour = ($row['colour']?$row['colour']:'yellow');
			echo "
				<div class='td_postit_title_".$colour."'>
				".
				($row['id_client']?"<a class='content_link' href='client_det.php?client=". $row['id_client']."'>".$row['clf']." ".$row['cll']."
				 </a>":"Post-it")
				."
				</div>";
			echo "<div class='td_postit_inner_".$colour."'>";
			echo "<table><tr><td><br/><br/><br/><br/></td><td width=100%><small>";
			echo $row['description'];
			echo "</small><tr><td colspan='2', style='text-align:right;'><small><i>";
			echo "From ".$row['auf']." ".$row['aul']." on ".format_date($row['date_creation'],date_short);
			echo '</i></small></td><tr>';
			echo "</td></tr></table>";
			echo "</div></td>";
			}	
		if ($cpt % 3 == 1)
			echo "<td style='width:33%'></td><td style='width:33%'></td></tr>";
		if ($cpt % 3 == 2)
			echo "<td style='width:33%'></td></tr>";

		echo "</table>";
		}
	else
		{
		lcm_bubble('no_notes');
		}
	show_new_note($ref);
	}
	
	
function show_new_note($ref='index.php')
	{
	echo '<form name="newnote" action="upd_note.php" method="post" onSubmit="var but = document.forms[\'editfu\'][\'submit\']; but.disabled=true; but.innerHTML=\'Please wait\'; return true;">';
	echo '<table class="tbl_usr_dtl" width="70%">' . "\n";
	echo '<tr><td>';
	echo "<tr><td>";
	echo "Recipient:";
	echo "</td><td>";
	echo "<select name='cc'>";
	echo "<option value='post-admin'>Admin Team</option>";
	echo "<option value='post-accom'>Accommodation Team</option>";
	echo "<option value='post-panel'>Panel</option>";
	echo "<option value='post-sal'>Advocacy</option>";
	echo "<option value='post-helpd'>Help Desk</option>";
	echo "<option value='post-bef'>Accompaniers</option>";
	echo "<option value='post-ns'>Night Shelter</option>";
	echo "<option value='post-friday'>Friday Team</option>";
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
	echo "<tr><td>";
	echo "Description:";
	echo "</td><td>";
	echo '<textarea  name="description" rows="15" cols="60" class="frm_tarea">';
	echo "</textarea>";
	echo "</td></tr>";
	echo '</table>';
	echo '<input type="hidden" name="ref" value="'.$ref.'">';
	echo "<p><button id=\"mr_submit\" name=\"submit\" type=\"submit\" value=\"submit\" class=\"simple_form_btn\">" . _T('button_validate') . "</button></p>\n";
	echo '</form>';
	}

?>
