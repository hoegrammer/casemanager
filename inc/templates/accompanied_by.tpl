<form method='POST' action="<?php echo $_SERVER['PHP_SELF'] . '?client=' . $client; ?>">
		<td class='td_strand'>
		    <div class='td_strand_title'>
			<small>Accompanying Info</small>
		    </div>
		    <table>
			<tr>
				<td>
					<label for='accompanied_by'><b><small>Regularly accompanied to Vulcan House By:</small></b></label>
				</td>
				<td>
					<input type='text'  name='accompanied_by' id='accompanied_by' value='<?php echo $accompanied_by;?>'/>
				</td>
			</tr>
			<tr>
				<td>
					<small>Leave blank if not accompanied there.</small>
				</td>
			</tr>
			<tr>
				<td>
				</td>
			</tr>
			<tr>
				<td>
					<input type='submit' value='Update'/>
				</td>
			</tr>

		    </table>
		</td>
	</tr>
    </table>
</form>
