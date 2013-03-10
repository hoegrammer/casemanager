<!-- in matt header div -->
	<div class="tabs">
		<ul class="tabs_list">
			<!-- only inactive tabs have link. Otherwise they get a bottom border -->
        		<li <?php if ($tab === 'print') echo 'class="active"'; ?>>
				<?php if ($tab !== 'print') echo '<a href="/casemanager/welfare_payments.php?&amp;tab=print">' ;?>
				Print Sheet
				<?php if ($tab !== 'print') echo '</a>';?>
			</li>
			<li <?php if ($tab === 'record') echo 'class="active"'; ?>>
				<?php if ($tab !== 'record') echo '<a href="/casemanager/welfare_payments.php?&amp;tab=record">' ;?>
				Record Payments Given
				<?php if ($tab !== 'record') echo '</a>';?>
			</li>
		</ul>
	</div>
</div> <!-- end of matt header div-->

