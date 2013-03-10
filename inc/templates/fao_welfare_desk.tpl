<form method='POST' action="<?php echo $_SERVER['PHP_SELF'] . '?client=' . $client; ?>">
    <table class='table_strands'>
	<tr><td>
	    <div class='td_strand_title'>
                FAO Welfare Desk
	    </div>
	    <table>
		<tr>
			<td>
				<b>Support this week:</b> 
			</td>
			<td>
				&pound;<input type='text' size=5 name='fao_amount' 
				value=<?php echo $faoWelfareDesk->amount; ?> />
			</td>
			<td> 
				Bus Pass <input type='checkbox' name='fao_bus_pass' 
				<?php if ($faoWelfareDesk->bus_pass) echo 'checked'; ?>
				 />
			</td>
			<td>
				<input type='submit' value='Save' />
			</td>
		</tr>
		<tr>
			<td>
				<b>Letter:</b>
			</td>
			<td>
				<input type='checkbox' name='fao_letter'
				<?php if ($faoWelfareDesk->letter) echo 'checked'; ?>
				/>
			</td>
			<td colspan=2 rowspan=3>
				<b>Note: </b><textarea rows='3' name='fao_note'><?php echo $faoWelfareDesk->note; ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<b>Advocacy Appointment:</b>
			</td>
			<td>
				<input type='checkbox' name='fao_advocacy'
				<?php if ($faoWelfareDesk->advocacy) echo 'checked'; ?>
				/>
			</td>
		</tr>
		<tr>
			<td>
				<b>Collect from Helpdesk:</b>
			</td>
			<td>
				<input type='checkbox' name='fao_from_helpdesk'
				<?php if ($faoWelfareDesk->from_helpdesk) echo 'checked'; ?>
				/>
			</td>
		</tr>
	    </table>
	</td></tr>
    </table>
</form>
