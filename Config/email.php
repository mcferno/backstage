<?php

// allow the bootstrap.env.php to override this
if(!class_exists('EmailConfig')) {

	class EmailConfig
	{
		public $default = array(
			'transport' => 'Mail',
			'from' => 'you@localhost',
			//'charset' => 'utf-8',
			//'headerCharset' => 'utf-8',
		);
	}

}