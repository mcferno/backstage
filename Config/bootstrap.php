<?php
// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array('engine' => 'File'));

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

define('ROLES_GENERAL',0);
define('ROLES_ADMIN',1);

define('MYSQL_DATE_FORMAT','Y-m-d H:i:s');

/**
 * Load any environment-specific configurations
 */
$bootstrap_environment = APP . 'Config' . DS . 'bootstrap.env.php';
if(file_exists($bootstrap_environment)) {
	include($bootstrap_environment);
}