<?php

/* Security measure */
if (!defined('IN_CMS')) { exit; }

if ($pagination_enabled) {
	use_helper('Pagination');
	$paginate = new Pagination(array(
		'base_url' => get_url('plugin/link_tracker/index/'),
		'total_rows' => $pg_total_row_count,
		'per_page' => $pg_rows_per_page,
		'cur_page' => $pg_current_page,
	));
}

$trackers_found = (isset($trackers) && !empty($trackers));

?>

<h1><?php echo __('Link Tracker'); ?></h1>

<?php if ($trackers_found): ?><form method="post" action="<?php echo $form_action; ?>"><?php endif; ?>

	<div class="data-table">
		
		<?php if (!$trackers_found): ?>
		
		<p><?php echo __('No records exist.'); ?></p>
		
		<p> <a href="<?php echo get_url('plugin/link_tracker/add'); ?>" title=""><?php echo __('Click here'); ?></a> <?php echo __('to create a record.'); ?></p>
		
		<?php else: ?>

		<div class="head">
			<ul>
				<li class="col id">ID</li>
				<li class="col name"><?php echo __('Name'); ?></li>
				<li class="col url"><?php echo __('URL'); ?></li>
				<li class="col action right"><?php echo __('Action'); ?></li>
				<li class="col total right"><?php echo __('# Clicks'); ?></li>
			</ul>
			<div class="clearfix"></div>
		</div>
		<!-- .head -->
		
		<div class="body">
				
				<ul>
					<?php foreach ($trackers as $tracker): ?>
					<li>
						<div class="col id"><?php echo $tracker->id; ?></div>
						<div class="col name">
							<a href="<?php echo get_url('plugin/link_tracker/edit/'.$tracker->id); ?>" title="Edit record '<?php echo $tracker->name; ?>'">
								<?php echo crop_text($tracker->name, 32); ?>
							</a>
						</div>
						<div class="col url">
							<span title="<?php echo $tracker->url; ?>">
								<?php echo crop_text($tracker->url, 32); ?>
							</span>
						</div>
						<div class="col action right"><input type="checkbox" name="tracker[<?php echo $tracker->id; ?>]" value="true" /></div>
						<div class="col total right"><?php echo $tracker->click_count; ?></div>
						<div class="clearfix"></div>
					</li>
					<?php endforeach; ?>
				</ul>
			
		</div>
		<!-- .body -->
		
		<?php endif; ?>
		
		<?php if ($pagination_enabled): ?>
		<div class="pagination"><?php echo $paginate->createLinks(); ?></div>
		<!-- .pagination -->
		<?php endif; ?>
		
	</div>
	<!-- .data-table -->
	
	<?php if ($trackers_found): ?>
	<p class="buttons">
		<select name="action">
			<option>No action</option>
			<option>Delete</option>
			<option>Reset stats</option>
		</select>
		&nbsp;&nbsp;
		<input type="submit" name="submit" class="button" value="<?php echo __('Update'); ?>">
	</p>
	<?php endif; ?>

<?php if ($trackers_found): ?></form><?php endif; ?>