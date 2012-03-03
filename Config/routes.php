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
 * Primary index pages, lists all content in paginated pages
 */
Router::connect('/', array('controller' => 'posts', 'action'=>'index', 'page'=>1));
Router::connect('/*', array('controller' => 'posts', 'action'=>'index'));

/**
 * CakePHP specifc routes (unused at the moment)
 */
//CakePlugin::routes();
//require CAKE . 'Config' . DS . 'routes.php';