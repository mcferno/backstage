<?php
// allow bootstrap.env.php to dictate the proper DB credentials
if(!class_exists('DATABASE_CONFIG')) {

class DATABASE_CONFIG {

	public $default = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'username',
		'password' => 'password',
		'database' => 'database',
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
}

}