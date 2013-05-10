<?php

/* Security measure */
if (!defined('IN_CMS')) { exit(); }

// Get database connection.
$conn = Record::getConnection();

// Delete tables.
$conn->exec("
	DROP TABLE `jb_link_trackers`;
	DROP TABLE `jb_link_tracker_clicks`;
");

// Delete settings.
$conn->exec("DELETE FROM `plugin_settings` WHERE plugin_id='link_tracker'");

exit;