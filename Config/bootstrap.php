<?php
App::uses('CakeLog', 'Log');

// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array('engine' => 'File'));

$engine = 'File';
$prefix = Configure::read('App.cache_prefix');

// short cache
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

Configure::write('Dispatcher.filters', array(
	//'AssetDispatcher',
	'CacheDispatcher'
));

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

define('MYSQL_DATE_FORMAT','Y-m-d H:i:s');

/**
 * App Configurations
 */
Configure::write('Site', array(
	// expiry of the remember-me login cookie (strtotime format)
	'rememberMeExpiry' => '+1 month',

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

		// minimum image size in pixels
		'minDimension' => 640,

		// maximum image size in pixels
		'maxDimension' => 1200
	)
));

/**
 * Load any environment-specific configurations
 */
$bootstrap_environment = APP . 'Config' . DS . 'bootstrap.env.php';
if(file_exists($bootstrap_environment)) {
	include($bootstrap_environment);
}