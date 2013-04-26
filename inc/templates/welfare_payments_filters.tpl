<div id='welfare_sheet_filter'>
   <form id='filter' action = "<?php echo $_SERVER['PHP_SELF'] . "?tab=$tab"; ?>" method="POST">
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

