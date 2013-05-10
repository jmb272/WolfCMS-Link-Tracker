<?php

/* Security measure */
if (!defined('IN_CMS')) { exit; }

if ($action == 'edit' && $pagination_enabled) {
	use_helper('Pagination');
	$paginate = new Pagination(array(
		'base_url' => get_url('plugin/link_tracker/edit/'.$tracker['id'].'/'),
		'total_rows' => $pg_total_row_count,
		'per_page' => $pg_rows_per_page,
		'cur_page' => $pg_current_page,
	));
}

?>

<h1><?php echo __(ucfirst(strtolower($action))); ?> <?php echo __('Tracker'); ?></h1>

<div id="form">
	<form method="post" action="<?php echo $form_action; ?>">
		
		<div class="row">
			<label>Name <span>*</span></label>
			<div class="field">
				<input type="text" class="textbox" name="tracker[name]" value="<?php echo $tracker['name']; ?>" />
			</div>
			<div class="clearfix"></div>
		</div>
		<!-- .row -->
		
		<div class="row">
			<label>URL<span>*</span></label>
			<div class="field">
				<input type="text" class="textbox" name="tracker[url]" value="<?php echo $tracker['url']; ?>" />
			</div>
			<div class="clearfix"></div>
		</div>
		<!-- .row -->
		
		<?php if ($action == 'edit'): ?>
		
		<div class="row">
			<label>Click count</label>
			<div class="field">
				<p><?php echo $tracker['click_count']; ?></p>
			</div>
			<div class="clearfix"></div>
		</div>
		<!-- .row -->
		
		<div class="row">
			<label>First clicked on</label>
			<div class="field">
				<p><?php echo $tracker['first_clicked_on']; ?></p>
			</div>
			<div class="clearfix"></div>
		</div>
		<!-- .row -->
		
		<div class="row">
			<label>Last clicked on</label>
			<div class="field">
				<p><?php echo $tracker['last_clicked_on']; ?></p>
			</div>
			<div class="clearfix"></div>
		</div>
		<!-- .row -->
		
		<?php endif; ?>
		
		<div class="row last">
			<div class="field">
				<p class="buttons">
					<input type="submit" class="button" name="submit[save]" value="Save" />
					<input type="submit" class="button" name="submit[edit]" value="Save and Continue Editing" />
					<?php if ($action == 'edit'): ?>
						<input type="button" class="button" id="reset-stats" value="Reset stats" />
					<?php endif; ?>
					or 
					<a href="<?php echo get_url('plugin/link_tracker'); ?>" title="">Cancel</a>
				</p>
			</div>
			<div class="clearfix"></div>
		</div>
		<!-- .row -->
		
	</form>
</div>

<?php if ($action == 'edit' && isset($tracker_clicks) && !empty($tracker_clicks)): ?>

<div id="clicks">
	<h3>Clicks</h3>
	<div class="links">
		<a href="<?php echo get_url('plugin/link_tracker/export_to_excel/'.$tracker['id']); ?>" title="">Export to Excel</a>
	</div>
	
	<?php if ($pagination_enabled): ?>
	<div class="pagination"><?php echo $paginate->createLinks(); ?></div>
	<!-- .pagination -->
	<?php endif; ?>
	
	<ul>
		<?php
		$i=0;
		foreach ($tracker_clicks as $click): 
			$date_1 = strtotime($click->datetime);
			$date_2 = date('l jS F, Y / H:i', $date_1);
			$datetime = $date_2;
		?>
		<li class="<?php echo ($i % 2 == 0 ? 'even' : 'odd'); ?>">
			<strong>Date / Time:</strong> <?php echo $datetime; ?> <br />
			<strong>IP Address:</strong> <?php echo (empty($click->ip_address) ? 'n/a' : $click->ip_address); ?> <br />
			<strong>User Agent:</strong> <?php echo (empty($click->user_agent) ? 'n/a' : $click->user_agent); ?> <br />
			<strong>Referer:</strong> <?php echo (empty($click->referer) ? 'n/a' : $click->referer); ?> <br />
		</li>
		<?php $i++; endforeach; ?>
	</ul>
	
	<?php if ($pagination_enabled): ?>
	<div class="pagination"><?php echo $paginate->createLinks(); ?></div>
	<!-- .pagination -->
	<?php endif; ?>
	
</div>
<?php endif; ?>

<script type="text/javascript">
$(document).ready(function() {
	$('input#reset-stats').show();
	$('input#reset-stats').click(function() {
		var confrm = confirm("Are you sure you want to reset this record's statistical data?");
		if (confrm) {
			window.location = '<?php echo get_url('plugin/link_tracker/reset_stats/'.$tracker['id']); ?>';
		}
	});	
});
</script>