<?php
class DATABASE_CONFIG {

	public $default = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'kennysays',
		'password' => 'jPyCFwyF346caBFb',
		'database' => 'kennyquotemachine',
		'encoding' => 'utf8'
	);
	public $live = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'mcferno_kqm',
		'password' => 'J4z_!3{@!3l~?,ufAk',
		'database' => 'mcferno_kqm',
		'encoding' => 'utf8'
	);
	public $test = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'root',
		'password' => 'mysql',
		'database' => 'testbed_test',
		'encoding' => 'utf8'
	);
	
	function __construct() {
		
		// detect the live server (web and console)
		if((isset($_SERVER['HTTP_HOST']) && stripos($_SERVER['HTTP_HOST'],'kennyquotemachine.com') !== false)
		|| (isset($_SERVER['HOSTNAME']) && stripos($_SERVER['HOSTNAME'],'hostgator.com') !== false)
		|| (isset($_SERVER['USER']) && stripos($_SERVER['USER'],'mcferno') !== false)) {
			$this->default = $this->live;
		}
	}
}
