<?php

/* Security measure */
if (!defined('IN_CMS')) { exit(); }

Plugin::setInfos(array(
    'id'                   => 'link_tracker',
    'title'                => __('Link Tracker'),
    'description'          => __('Track links throughout your WolfCMS website.'),
    'version'              => '1.0.0',
   	'license'              => 'GPL',
	'author'               => 'James Bailey',
    'website'              => 'http://blog.james-bailey.com/',
    'update_url'           => 'http://www.wolfcms.org/plugin-versions.xml',
    'require_wolf_version' => '0.5.5',
	'type'                 => 'both'
));

$plugin_dir = PLUGINS_ROOT . DS . 'link_tracker' . DS;

// Load misc function library.
include($plugin_dir.'functions.php');

// Load controller.
Plugin::addController('link_tracker', __('Link Tracker'), 'admin_view', true);

// Load models.
AutoLoader::addFolder($plugin_dir . 'models');

// Get out url slug.
$out_slug = Plugin::getSetting('out_slug', 'link_tracker');

Dispatcher::addRoute(array(
	'/'.$out_slug.'/:num' => 'plugin/link_tracker/track/$1'
));
