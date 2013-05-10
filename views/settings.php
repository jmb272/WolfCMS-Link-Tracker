<?php

/* Security measure */
if (!defined('IN_CMS')) { exit(); }

?>

<h1><?php echo __('Settings'); ?></h1>

<form method="post" action="<?php echo $form_action; ?>">

	<div class="row last">
		<label><?php echo __('Outbound URL Slug'); ?></label>
		<div class="field">
			<input type="text" class="textbox" name="settings[out_slug]" value="<?php echo $settings['out_slug']; ?>" />
			<br><small>http://www.site.co.uk/OUT_SLUG/id</small>
		</div>
		<div class="clearfix"></div>
	</div>
	<!-- .row -->
	
	<h3><?php echo __('Click Details'); ?></h3>
	<p><?php echo __('If you choose to store click details, everytime a link is clicked the IP Address, Referer and User Agent of the user is recorded.'); ?></p>
	
	<div class="row">
		<label><?php echo __('Store click details'); ?></label>
		<div class="field">
			<input type="radio" name="settings[store_click_details]" value="0"<?php echo ($settings['store_click_details'] == '0' ? ' checked="checked"' : ''); ?> /> <?php echo __('No'); ?>
			&nbsp;&nbsp;
			<input type="radio" name="settings[store_click_details]" value="1"<?php echo ($settings['store_click_details'] == '1' ? ' checked="checked"' : ''); ?> /> <?php echo __('Yes'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<!-- .row -->
	
	<div class="row">
		<label><?php echo __('Save # Click events'); ?> </label>
		<div class="field">
			<input type="text" class="textbox" name="settings[saved_clicks_per_tracker]" value="<?php echo $settings['saved_clicks_per_tracker']; ?>" />
			<br><small>0 = <?php echo __('no limit'); ?></small>
		</div>
		<div class="clearfix"></div>
	</div>
	<!-- .row -->
	
	<h3><?php echo __('Lock IP Address'); ?></h3>
	<p><?php echo __('Only count one click on a link per IP address per day.'); ?></p>
	
	<div class="row last">
		<label><?php echo __('Lock IP Address'); ?></label>
		<div class="field">
			<input type="radio" name="settings[lock_ip_address]" value="0"<?php echo($settings['lock_ip_address'] == '0' ? ' checked="checked"' : ''); ?>  /> <?php echo __('No'); ?>
			&nbsp;&nbsp;
			<input type="radio" name="settings[lock_ip_address]" value="1"<?php echo ($settings['lock_ip_address'] == '1' ? ' checked="checked"' : ''); ?> /> <?php echo __('Yes'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<h3><?php echo __('Lock the Referer'); ?></h3>
	<p><?php echo __('Only count clicks originating from this site.'); ?></p>
	
	<div class="row last">
		<label><?php echo __('Referer'); ?></label>
		<div class="field">
			<input type="text" class="textbox" name="settings[lock_referer]" value="<?php echo $settings['lock_referer']; ?>" />
			<br><small><strong><?php echo __('Recommended value'); ?>:</strong> <?php echo remove_trail(get_url(), '/admin/'); ?></small>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<h3><?php echo __('Pagination'); ?></h3>
	<p><?php echo __('Split up results onto multiple pages.'); ?></p>
	
	<div class="row">
		<label><?php echo __('Paginate results'); ?></label>
		<div class="field">
			<input type="radio" name="settings[paginate]" value="0"<?php echo($settings['paginate'] == '0' ? ' checked="checked"' : ''); ?>  /> <?php echo __('No'); ?>
			&nbsp;&nbsp;
			<input type="radio" name="settings[paginate]" value="1"<?php echo ($settings['paginate'] == '1' ? ' checked="checked"' : ''); ?> /> <?php echo __('Yes'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<!-- .row -->
	
	<div class="row last">
		<label><?php echo __('Rows per page'); ?></label>
		<div class="field">
			<input type="text" class="textbox" name="settings[rows_per_page]" value="<?php echo $settings['rows_per_page']; ?>" />
		</div>
		<div class="clearfix"></div>
	</div>
	<!-- .row -->
	
	<h3><?php echo __('Sorting'); ?></h3>
	<p><?php echo __('Choose in what order to display the data on the index page.'); ?></p>
	
	<div class="row">
		<label><?php echo __('Sort by'); ?></label>
		<div class="field">
			<?php if (!isset($sort_fields) || empty($sort_fields)): ?>
			<?php echo __('No sort fields provided.'); ?>
			<?php else: ?>
			<select name="settings[sort_field]">
				<?php foreach ($sort_fields as $key => $sort_field): ?>
				<option value="<?php echo $sort_field; ?>"<?php echo ($settings['sort_field'] == $sort_field ? ' selected="selected"' : ''); ?>><?php echo $sort_field; ?></option>
				<?php endforeach; ?>
			</select>
			&nbsp;in&nbsp;
			<select name="settings[sort_order]">
				<option value="asc"<?php echo ($settings['sort_order'] == 'asc' ? ' selected="selected"' : ''); ?>><?php echo __('Ascending'); ?></option>
				<option value="desc"<?php echo ($settings['sort_order'] == 'desc' ? ' selected="selected"' : ''); ?>><?php echo __('Descending'); ?></option>
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
				<input type="submit" class="button" name="submit[save]" value="<?php echo __('Save'); ?>" />
				<input type="submit" class="button" name="submit[edit]" value="<?php echo __('Save and Continue Editing'); ?>" />
				or 
				<a href="<?php echo get_url('plugin/link_tracker'); ?>" title=""><?php echo __('Cancel'); ?></a>
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