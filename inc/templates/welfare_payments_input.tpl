<!-- Template for welfare payments input form -->

<form action = 'welfare_payments_save.php' method = 'POST'>
	<br />
	<table align='center' class='tbl_usr_dtl' width='97%'>
		<tr>
			<th class='td_strand_title'>Client</th>
			<th class='td_strand_title'>Cash</th>
			<th class='td_strand_title'>Bus Pass</th>
			<th class='td_strand_title'>Notes</th>
			<th class='td_strand_title'>Absent</th>
		</tr>
		<?php 
		foreach ($clients as $client) {
			// alternating highlighted rows 
			$highlight = !$highlight;
			$class = $highlight ? 'tbl_cont_dark' : 'tbl_cont_light';
		?>
		<tr class="welfare_input <?php echo $class; ?>">
			<td>
				<!-- client name, as link -->
				<a class='content_link' 
				href="client_det.php?client=<?php echo $client[id_client]; ?>">
				<?php echo $client[name_first] . ' ' .  $client[name_last]; ?>
				</a>
			</td>
			<td>	
				<input type='hidden' name="id_case[]"; 
					value="<?php echo $client['id_case'] ?>" />
				&pound;<input type='text' size='5px' name="amount[<?php echo $client['id_case']; ?>]"/>
			</td>
			<td>
                                <input type='checkbox' class='bus_pass_checkbox'
				name="bus_pass[<?php echo $client['id_case']; ?>]"  />
                        </td>
			<td>
                                <input type='text' size='80px' name="note[<?php echo $client['id_case']; ?>]"  />
                        </td>
			<td class = 'centred'>
                                <input type='checkbox' name="absent[<?php echo $client['id_case']; ?>]"  />
                        </td>

	
		</tr>
	        <?php } ?>
	</table>
	<input type='submit' value='save' id='welfare_input_submit' />
</form>
