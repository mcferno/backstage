<?php
// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array('engine' => 'File'));

CakePlugin::load('Postable');

define('ROLES_GENERAL',0);
define('ROLES_ADMIN',1);

define('MYSQL_DATE_FORMAT','Y-m-d H:i:s');