<?php

/* Security measure */
if (!defined('IN_CMS')) { exit; }

// Create database.
$create_sql = "
CREATE TABLE IF NOT EXISTS `jb_link_trackers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `url` varchar(255) NOT NULL,
  `click_count` int(10) unsigned NOT NULL,
  `first_clicked_on` datetime NOT NULL,
  `last_clicked_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE IF NOT EXISTS `jb_link_tracker_clicks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `link_tracker_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `referer` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

$conn = Record::getConnection();
$conn->exec($create_sql);

// Insert settings.
$plugin_settings = Plugin::getAllSettings('link_tracker');
if (empty($plugin_settings)) 
{
	$settings = array(
		// site.co.uk/out_slug/id
		'out_slug' => 'out',
		
		// pagination settings.
		'paginate' => '1',
		'rows_per_page' => '10',
		
		// sort settings.
		'sort_field' => 'id',
		'sort_order' => 'desc',
		
		// lock referer.
		'lock_referer' => remove_trail(get_url(), 'admin/'),
		
		// lock ip address.
		'lock_ip_address' => '0',
		
		// store detailed click data e.g. user agent, referer, ip address.
		'store_click_details' => '1',
		
		// amount of click instances to store detailed data on per record.
		'saved_clicks_per_tracker' => '10'
	);
	
	Plugin::setAllSettings($settings, 'link_tracker');
}

exit;
