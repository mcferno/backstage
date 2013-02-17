<?php
App::uses('CakeLog', 'Log');

// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array('engine' => 'File'));

$engine = 'File';

// short cache
Cache::config('short', array(
	'engine' => $engine,
	'prefix' => 'app_short_',
	'path' => CACHE . 'persistent' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => '5 minutes'
));

Cache::config('online_status', array(
	'engine' => $engine,
	'prefix' => 'app_online_status_',
	'path' => CACHE . 'persistent' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => '20 seconds'
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

// time in seconds after which chat messages are no longer retrievable
define('MESSAGES_DEFAULT_ROLLOVER', 14400); // 4 hrs

// number of chat messages to retrieve
define('MESSAGES_DEFAULT_BUFFER', 40);

/**
 * Load any environment-specific configurations
 */
$bootstrap_environment = APP . 'Config' . DS . 'bootstrap.env.php';
if(file_exists($bootstrap_environment)) {
	include($bootstrap_environment);
}