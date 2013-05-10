<?php

/* Security measure */
if (!defined('IN_CMS')) { exit(); }

?>

<h1><?php echo __('Settings'); ?></h1>

<form method="post" action="<?php echo $form_action; ?>">

	<div class="row last">
		<label>Outbound URL Slug</label>
		<div class="field">
			<input type="text" class="textbox" name="settings[out_slug]" value="<?php echo $settings['out_slug']; ?>" />
			<br><small>http://www.site.co.uk/OUT_SLUG/id</small>
		</div>
		<div class="clearfix"></div>
	</div>
	<!-- .row -->
	
	<h3>Click Details</h3>
	<p>If you choose to store click details, everytime a link is clicked the IP Address, Referer and User Agent of the user is recorded.</p>
	
	<div class="row">
		<label>Store click details</label>
		<div class="field">
			<input type="radio" name="settings[store_click_details]" value="0"<?php echo ($settings['store_click_details'] == '0' ? ' checked="checked"' : ''); ?> /> No
			&nbsp;&nbsp;
			<input type="radio" name="settings[store_click_details]" value="1"<?php echo ($settings['store_click_details'] == '1' ? ' checked="checked"' : ''); ?> /> Yes
		</div>
		<div class="clearfix"></div>
	</div>
	<!-- .row -->
	
	<div class="row">
		<label>Save # Click events </label>
		<div class="field">
			<input type="text" class="textbox" name="settings[saved_clicks_per_tracker]" value="<?php echo $settings['saved_clicks_per_tracker']; ?>" />
			<br><small>0 = no limit</small>
		</div>
		<div class="clearfix"></div>
	</div>
	<!-- .row -->
	
	<h3>Lock IP Address</h3>
	<p>Only count one click on a link per IP address per day.</p>
	
	<div class="row last">
		<label>Lock IP Address</label>
		<div class="field">
			<input type="radio" name="settings[lock_ip_address]" value="0"<?php echo($settings['lock_ip_address'] == '0' ? ' checked="checked"' : ''); ?>  /> No
			&nbsp;&nbsp;
			<input type="radio" name="settings[lock_ip_address]" value="1"<?php echo ($settings['lock_ip_address'] == '1' ? ' checked="checked"' : ''); ?> /> Yes
		</div>
		<div class="clearfix"></div>
	</div>
	
	<h3>Lock the Referer</h3>
	<p>Only count clicks originating from this site.</p>
	
	<div class="row last">
		<label>Referer</label>
		<div class="field">
			<input type="text" class="textbox" name="settings[lock_referer]" value="<?php echo $settings['lock_referer']; ?>" />
			<br><small><strong>Recommended value:</strong> <?php echo remove_trail(get_url(), '/admin/'); ?></small>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<h3>Pagination</h3>
	<p>Split up results onto multiple pages (or not).</p>
	
	<div class="row">
		<label>Paginate results</label>
		<div class="field">
			<input type="radio" name="settings[paginate]" value="0"<?php echo($settings['paginate'] == '0' ? ' checked="checked"' : ''); ?>  /> No
			&nbsp;&nbsp;
			<input type="radio" name="settings[paginate]" value="1"<?php echo ($settings['paginate'] == '1' ? ' checked="checked"' : ''); ?> /> Yes
		</div>
		<div class="clearfix"></div>
	</div>
	<!-- .row -->
	
	<div class="row last">
		<label>Rows per page</label>
		<div class="field">
			<input type="text" class="textbox" name="settings[rows_per_page]" value="<?php echo $settings['rows_per_page']; ?>" />
		</div>
		<div class="clearfix"></div>
	</div>
	<!-- .row -->
	
	<h3>Sorting</h3>
	<p>Choose in what order to display the data on the index page.</p>
	
	<div class="row">
		<label>Sort by</label>
		<div class="field">
			<?php if (!isset($sort_fields) || empty($sort_fields)): ?>
			No sort fields provided.
			<?php else: ?>
			<select name="settings[sort_field]">
				<?php foreach ($sort_fields as $key => $sort_field): ?>
				<option value="<?php echo $sort_field; ?>"<?php echo ($settings['sort_field'] == $sort_field ? ' selected="selected"' : ''); ?>><?php echo $sort_field; ?></option>
				<?php endforeach; ?>
			</select>
			&nbsp;in&nbsp;
			<select name="settings[sort_order]">
				<option value="asc"<?php echo ($settings['sort_order'] == 'asc' ? ' selected="selected"' : ''); ?>>Ascending</option>
				<option value="desc"<?php echo ($settings['sort_order'] == 'desc' ? ' selected="selected"' : ''); ?>>Descending</option>
			</select>&nbsp;
			order
			<?php endif; ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<!-- .row -->
	
	<div class="row last">
		<div class="field">
			<p class="buttons">
				<input type="submit" class="button" name="submit[save]" value="Save" />
				<input type="submit" class="button" name="submit[edit]" value="Save and Continue Editing" />
				or 
				<a href="<?php echo get_url('plugin/link_tracker'); ?>" title="">Cancel</a>
			</p>
		</div>
		<div class="clearfix"></div>
	</div>
	<!-- .row -->
	
</form>

<script type="text/javascript">
// <![CDATA[
    function setConfirmUnload(on, msg) {
        window.onbeforeunload = (on) ? unloadMessage : null;
        return true;
    }

    function unloadMessage() {
        return '<?php echo __('You have modified this page.  If you navigate away from this page without first saving your data, the changes will be lost.'); ?>';
    }

    $(document).ready(function() {
        // Prevent accidentally navigating away
        $(':input').bind('change', function() { setConfirmUnload(true); });
        $('form').submit(function() { setConfirmUnload(false); return true; });
    });
// ]]>
</script>