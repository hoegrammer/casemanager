<div id='welfare_sheet_filter'>
   <form id='filter' action = "<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
	<label for='collect_from'>Collect From:</label>	
	<select id='collect_from' name='collect_from' onChange="document.getElementById('filter').submit()">
		<option value='any'>Any</option>
		<option value='welfare' <?php echo $welfare_selected;?>>Welfare Desk</option>
		<option value='help' <?php echo $help_selected;?>>Help Desk</option>
	</select>

	<label for='support_type'>Support Type:</label>	
	<select id='support_type' name='support_type' onChange="document.getElementById('filter').submit()">>
		<option value='any'>Any</option>
		<option value='accommodated' <?php echo $accommodated_selected;?>>Accommodated</option>
		<option value='not_accommodated' <?php echo $not_accommodated_selected;?>>Not Accommodated</option>
	</select>
   </form>
</div>

<table id='welfare_sheet' border=1>
	<thead border=1 height = '60px'>
		<th>Client</th>
		<th>Letter</th>
		<th width='20px'>Adv. appt</th>
		<th>Collect From</th>
		<th>Note</th>
		<th width='60px'>Usual</th>
		<th width='60px'>This Week</th>
		<th>Cash given</th>
		<th>BP given</th>
		<th>ID</th>
		<th width='120px'>Signed</th>
	</thead>
	<?php foreach($rows as $row) { ?>
	<tr>
		<td><?php echo $row->client_name; ?></td>
		<td><?php echo $row->letter ? '&#10004;' : ''; ?></td>
		<td><?php echo $row->advocacy ? '&#10004;' : ''; ?></td>
		<td><?php echo $row->collect_from; ?></td>
		<td><?php echo $row->note; ?></td>
		<td><?php echo $row->usualSupport; ?></td>
		<td><?php echo $row->thisWeekSupport; ?></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<?php } ?>
</table>
<table id='welfare_sheet_summary' border=1>
	<thead>
		<th>Summary</th>
	</thead>
	<?php foreach ($summary->amounts as $pounds => $quantity) { ?>
	<tr>
		<td><?php echo "$quantity x &pound;$pounds"; ?></td>
	</tr>
        <?php } ?>
	<tr>
		<td><?php echo "Total: &pound;" . $summary->total; ?></td>
	</tr>
	<tr>
		<td><?php echo "Bus Passes: " . $summary->busPasses; ?></td>
	</tr>


</table>
