<?php

/* Security measure */
if (!defined('IN_CMS')) { exit; }

?>

<p class="button">
	<a href="<?php echo get_url('plugin/link_tracker/add'); ?>" title="Add tracker"> <img align="middle" src="<?php echo URI_PUBLIC; ?>wolf/icons/action-add-32.png" alt="" /> <?php echo __('Add tracker'); ?> </a>
</p>
<p class="button">
	<a href="<?php echo get_url('plugin/link_tracker'); ?>" title="View trackers"> <img align="middle" src="<?php echo URI_PUBLIC; ?>wolf/icons/cloud-32.png" alt="" /> <?php echo __('Manage trackers'); ?> </a>
</p>
<p class="button">
	<a href="<?php echo get_url('plugin/link_tracker/settings'); ?>" title="Change Link Tracker's settings">
		<img align="middle" src="<?php echo URI_PUBLIC; ?>wolf/icons/settings-32.png" alt="" /> <?php echo __('Settings'); ?>
	</a>
</p>
<p class="button">
	<a href="<?php echo get_url('plugin/link_tracker/documentation'); ?>" title="Read the documentation">
		<img align="middle" src="<?php echo URI_PUBLIC; ?>wolf/icons/documentation-32.png" alt="" /> <?php echo __('Documentation'); ?>
	</a>
</p>