<?php

/**
 * App configurations
 */

Configure::write('Site', array(

	// choose the site title
	'name' => 'Backstage',

	// expiry of the remember-me login cookie (strtotime format)
	'rememberMeExpiry' => '+1 month',

	// whether to present the authed back-end at the base URL
	'showPublicPages' => false,

	'Chat' => array(
		// expiry of messages in live chat (time in seconds)
		'messageExpiry' => 14400,

		// maximum message count in live chat history
		'maxHistoryCount' => 40
	),

	'Images' => array(
		// pagination limits for desktop users
		'perPage' => 60,

		// pagination limits for mobile
		'perPageMobile' => 30,

		// number of recent albums to show
		'recentAlbums' => 1,

		// number of images to preview from each album
		'albumPreviews' => 6,

		// minimum image size in pixels
		'minDimension' => 640,

		// maximum image size in pixels
		'maxDimension' => 1200
	)
));

/**
 * General configurations
 */

App::uses('CakeLog', 'Log');

CakeLog::config('debug', array(
	'engine' => 'FileLog',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));
CakeLog::config('error', array(
	'engine' => 'FileLog',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));

CakePlugin::load(array('Postable'));

Configure::write('Dispatcher.filters', array(
	'CacheDispatcher'
));

define('MYSQL_DATE_FORMAT','Y-m-d H:i:s');

/**
 * Caching configurations
 */

$engine = 'File';
if (extension_loaded('apc') && (php_sapi_name() !== 'cli' || ini_get('apc.enable_cli'))) {
	$engine = 'Apc';
}

$duration = '+999 days';
if (Configure::read('debug') >= 1) {
	$duration = '+1 second';
}

Configure::write('App.cache_engine', $engine);
Configure::write('App.cache_duration_default', $duration);
Configure::write('App.cache_prefix', 'kqm_');

/**
 * Load any environment-specific configurations
 */

$bootstrap_environment = APP . 'Config' . DS . 'bootstrap.env.php';
if(file_exists($bootstrap_environment)) {
	include($bootstrap_environment);
}

/**
 * Configure cacheable instances
 */

$engine = Configure::read('App.cache_engine');
$duration = Configure::read('App.cache_duration_default');
$prefix = Configure::read('App.cache_prefix');

Cache::config('default', array('engine' => $engine));

Cache::config('_cake_core_', array(
	'engine' => $engine,
	'prefix' => $prefix . 'cake_core_',
	'path' => CACHE . 'persistent' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => $duration
));

Cache::config('_cake_model_', array(
	'engine' => $engine,
	'prefix' => $prefix . 'cake_model_',
	'path' => CACHE . 'models' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => $duration
));

Cache::config('short', array(
	'engine' => $engine,
	'prefix' => $prefix . 'app_short_',
	'path' => CACHE . 'persistent' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => '5 minutes'
));

Cache::config('online_status', array(
	'engine' => $engine,
	'prefix' => $prefix . 'app_online_status_',
	'path' => CACHE . 'persistent' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => '10 seconds'
));
