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
				&nbsp; Bus Pass <input type='checkbox' name='fao_bus_pass' 
				<?php if ($faoWelfareDesk->bus_pass) echo 'checked'; ?>
				 />
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
		<tr>
			<td>
				<b>Note: </b>
			</td>
			<td>
				<input type='textarea' name='fao_note'
				value  ="<?php echo $faoWelfareDesk->note; ?>" />
				<input type='submit' value='Save' />
			</td>
		</tr>

	    </table>
	</td></tr>
    </table>
</form>
