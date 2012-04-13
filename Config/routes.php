<?php

/**
 * Additional features of the site
 */
Router::connect('/generator', array('controller' => 'pages', 'action'=>'quote_generator'));
Router::connect('/refresh', array('controller' => 'posts', 'action'=>'refresh'));

/**
 * Single post view pages, direct, and seo-friendly
 */
Router::connect('/post/:id', array('controller' => 'posts', 'action'=>'view'));
Router::connect('/post/:slug/:id', 
	array('controller' => 'posts', 'action'=>'view'),
	array('slug'=>'[a-zA-Z0-9-_\/]+?')
);

/**
 * Backstage, the administrative panel
 */
// Setting the admin urls manually, due to differing uri name
Router::connect("/backstage", array('controller'=>'users', 'action' => 'login', 'prefix' => 'admin', 'admin' => true));
Router::connect("/backstage/dashboard", array('controller'=>'users', 'action' => 'dashboard', 'prefix' => 'admin', 'admin' => true));
Router::connect("/backstage/meme-generator", array('controller'=>'pages', 'action' => 'meme_generator', 'prefix' => 'admin', 'admin' => true));
Router::connect("/backstage/setup", array('controller' => 'users', 'action' => 'setup', 'prefix', 'admin', 'admin' => true));

Router::connect("/backstage/:controller", array('action' => 'index', 'prefix' => 'admin', 'admin' => true));
Router::connect("/backstage/:controller/:action/*", array('prefix' => 'admin', 'admin' => true));


/**
 * Primary index pages, lists all content in paginated pages
 */

// Isolate the homepage
Router::connect('/', array('controller' => 'posts', 'action'=>'home', 'page'=>1));
Router::connect('/', array('controller' => 'posts', 'action'=>'index', 'page'=>1)); // reverse-routing only (dupped)

Router::connect('/*', array('controller' => 'posts', 'action'=>'index'));

/**
 * CakePHP specifc routes (unused at the moment)
 */
//CakePlugin::routes();
//require CAKE . 'Config' . DS . 'routes.php';