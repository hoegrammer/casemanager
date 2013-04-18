<!-- Template for welfare payments report -->

	<br />
	<table align='center' class='tbl_usr_dtl' width='97%'>
		<tr>
			<th class='td_strand_title'>Client</th>
			<!-- A heading for each date -->
			<?php foreach ($report->dates as $date) { ?>
			<th class='td_strand_title' colspan = 2><?php echo $date;?></th>
			<?php } ?>
		</tr>
		<tr>
			<th class='td_strand_title'></th> <!-- client heading is 2 rows high -->
 			<!-- under each date, subheadings for amount and bus pass  -->
			<?php foreach ($report->dates as $date) { ?>
			<th class='td_strand_title'>Cash</th>
			<th class='td_strand_title'>Bus Pass</th>
			<?php } ?>
		</tr>
		<?php 
		foreach ($report->rows as $reportRow) {
			// alternating highlighted rows 
			$highlight = !$highlight;
			$class = $highlight ? 'tbl_cont_dark' : 'tbl_cont_light';
		?>
		<tr class="welfare_input <?php echo $class; ?>">
			<td>
				<!-- client name, as link -->
				<?php echo $reportRow->link;?>
			</td>
			<!-- A column for each date -->
			<?php foreach ($report->dates as $date) {
				foreach($reportRow->entries as $entry) { 
					if ($entry->date === $date) { ?>
			<td>
				&pound;<?php echo $entry->amount;?>
			</td>
			<td>
                                <input type='checkbox' disabled = "disabled "class='bus_pass_checkbox'
						<?php if ($entry->bus_pass) { ?>
				checked = 'checked'
						<?php } ?>
				/>
					<?php } ?> <!-- end if entry date = heading date -->
				<?php } ?> <!-- end foreach entry -->
                        </td>
			<?php } ?> <!-- end foreach date -->
		</tr>
	        <?php } ?> <!-- end foreach row -->
	</table>
