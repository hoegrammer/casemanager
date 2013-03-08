<!-- Template for welfare payments input form -->

<form action = 'welfare_payments_save.php' method = 'POST'>
	<table border = 1>
		<tr>
			<th>Client</th>
			<th>Cash</th>
			<th>Buss Pass</th>
			<th>Notes</th>
		</tr>
		<?php 
		foreach ($clients as $client) { ?>
		<tr>
			<td>
				<?php echo $client[name_first] . ' ' .  $client[name_last]; ?>
			</td>
			<td>	
				<input type='hidden' name="id_case[]"; 
					value="<?php echo $client['id_case'] ?>" />
				&pound;<input type='text' size='5px' name="amount[<?php echo $client['id_case']; ?>]"/>
			</td>
			<td>
                                <input type='checkbox' name="bus_pass[<?php echo $client['id_case']; ?>]"  />
                        </td>
			<td>
                                <input type='text' size='100px' name="note[<?php echo $client['id_case']; ?>]"  />
                        </td>
	
		</tr>
	        <?php } ?>
	</table>
	<input type='submit' />
</form>
