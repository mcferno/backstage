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
		if(stripos($_SERVER['HTTP_HOST'],'kennyquotemachine.com') !== false) {
			$this->default = $this->live;
		}
	}
}
