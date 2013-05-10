<?php

/* Security measure */
if (!defined('IN_CMS')) { exit(); }

?>

<h1><?php echo __('Documentation'); ?></h1>

<p>
	The purpose of this plugin is to track specific links around your website, 
	see how many clicks they get and who clicks them.
</p>

<h3>How it works</h3>
<p>
	You create a new record by clicking <a href="<?php echo get_url('plugin/link_tracker/add'); ?>">Add Tracker</a> in the 
	sidebar.
</p>
<p>
	You then take the ID of this record (found on the main page next to its name),
	your websites URL and the outgoing URL slug as defined in the <a href="<?php echo get_url('plugin/link_tracker/settings'); ?>">plugin settings</a> 
	to create the tracker URL.
	<br><br>
	e.g.<br>
	http://www.site.co.uk/out/3
</p>
<p>
	You then use this URL throughout your website to track who clicks on it and when 
	it gets clicked.
</p>

<h3>Settings</h3>
<p>Here is a simple overview of each setting, so you know how to configure it to suit your needs.</p>

<ul>
	<li>
		<p>
			<strong>Outbound URL slug</strong>
			<br />
			This is the part of the URL that redirects the user's request to the tracking script.
			It can be set to whatever you want, just make sure it doesn't conflict with a page.
		</p>
	</li>
	<li>
		<p>
			<strong>Store click details</strong>
			<br />
			If enabled, when a tracker link is clicked the Referer, Browser, IP Address and Date/time is recorded
			and can be viewed on the record's edit page.
		</p>
	</li>
	<li>
		<p>
			<strong>Save # click events</strong>
			<br />
			This value represents the amount of click events to record per link tracker record. If set to 0, there
			is no limit.
		</p>
	</li>
	<li>
		<p>
			<strong>Lock IP Address</strong>
			<br />
			If enabled, only one click per link, per IP address, per day will be recorded. Recommended as an anti-abuse measure.
		</p>
	</li>
	<li>
		<p>
			<strong>Lock Referer</strong>
			<br />
			This value is checked against the referer of each click request, if the referer of a click request doesn't
			begin with the lock_referer value, it isn't recorded. <br />If empty, the referer isn't checked.
		</p>
	</li>
	<li>
		<p>
			<strong>Paginate results</strong>
			<br />
			If enabled, this setting will split up the link tracker records on the index page into multiple pages with
			links provided to the other pages.
		</p>
	</li>
	<li>
		<p>
			<strong>Rows per page</strong>
			<br />
			If pagination is enabled (see last setting), this setting dictates how many records will be displayed per page.
		</p>
	</li>
	<li>
		<p>
			<strong>Sort by</strong>
			<br />
			These values decide by what field and in what order the link tracker records on the index page should be displayed in.<br />
			For example, you can change the default values to show the records with the highest number of clicks first.
		</p>
	</li>
</ul>