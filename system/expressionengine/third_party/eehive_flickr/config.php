<?php if ( ! defined('APP_VER')) exit('No direct script access allowed');

if (! defined('EEHIVE_FLICKR_NAME'))
{
	define('EEHIVE_FLICKR_NAME', 'Flickr');
	define('EEHIVE_FLICKR_VER',  '2.1.0');
	define('EEHIVE_FLICKR_AUTHOR', 'EE Hive - Brett DeWoody');
	define('EEHIVE_FLICKR_DESC', 'Provides tags for integrating Flickr into your website');
	define('EEHIVE_FLICKR_DOCS', 'http://www.ee-hive.com/expressionengine-2/flickr');
}

// NSM Addon Updater
$config['name'] = EEHIVE_FLICKR_NAME;
$config['version'] = EEHIVE_FLICKR_VER;
$config['nsm_addon_updater']['versions_xml'] = 'http://www.ee-hive.com/expressionengine-2/flickr';
