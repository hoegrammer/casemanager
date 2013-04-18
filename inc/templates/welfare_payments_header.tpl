<!-- in matt header div -->
	<div class="tabs">
		<ul class="tabs_list">
			<!-- only inactive tabs have link. Otherwise they get a bottom border -->
        		<li <?php if ($tab === 'print') echo 'class="active"'; ?>>
				<?php if ($tab !== 'print') echo '<a href='. $_SERVER['PHP_SELF'] .'?tab=print>' ;?>
				Print Sheet
				<?php if ($tab !== 'print') echo '</a>';?>
			</li>
			<li <?php if ($tab === 'record') echo 'class="active"'; ?>>
				<?php if ($tab !== 'record') echo '<a href='. $_SERVER['PHP_SELF'] .'?tab=record>' ;?>
				Record Payments Given
				<?php if ($tab !== 'record') echo '</a>';?>
			</li>
			<li <?php if ($tab === 'report') echo 'class="active"'; ?>>
				<?php if ($tab !== 'report') echo '<a href='. $_SERVER['PHP_SELF'] .'?tab=report>' ;?>
				Report				
				<?php if ($tab !== 'report') echo '</a>';?>
			</li>

		</ul>
	</div>
</div> <!-- end of matt header div-->

