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
 * TOS and Privacy Statements
 */
Router::connect('/terms', array('controller' => 'pages', 'action'=>'display', 'terms'));
Router::connect('/privacy-policy', array('controller' => 'pages', 'action'=>'display', 'privacy_policy'));

/**
 * Backstage, the administrative panel
 */
// Setting the admin urls manually, due to differing uri name
$admin_url = 'backstage';
Router::connect("/{$admin_url}", array('controller'=>'users', 'action' => 'login', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/dashboard", array('controller'=>'users', 'action' => 'dashboard', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/dashboard/updates/*", array('controller'=>'users', 'action' => 'updates', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/meme-generator/*", array('controller'=>'pages', 'action' => 'meme_generator', 'prefix' => 'admin', 'admin' => true));

/**
 * Image Assets
 */
Router::connect("/{$admin_url}/my-images/*", array('controller'=>'assets', 'action' => 'index', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/albums/set_cover/*", array('controller'=>'albums', 'action' => 'set_cover', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/albums/save/*", array('controller'=>'albums', 'action' => 'save', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/albums/delete/*", array('controller'=>'albums', 'action' => 'delete', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/albums/*", array('controller'=>'assets', 'action' => 'albums', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/all-images/*", array('controller'=>'assets', 'action' => 'users', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/user/*", array('controller'=>'assets', 'action' => 'user', 'prefix' => 'admin', 'admin' => true));

Router::connect("/{$admin_url}/chat", array('controller'=>'users', 'action' => 'group_chat', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/setup", array('controller' => 'users', 'action' => 'setup', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/quotes/*", array('controller' => 'posts', 'action'=> 'index', 'prefix' => 'admin', 'admin' => true));

Router::redirect("/{$admin_url}/page", "/{$admin_url}/dashboard");
Router::connect("/{$admin_url}/page/:uri", array('controller' => 'pages', 'action'=> 'content', 'prefix' => 'admin', 'admin' => true), array('uri' => '.+'));

Router::connect("/{$admin_url}/caption-battles", array('controller'=>'contests', 'action' => 'index', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/caption-battles/:action/*", array('controller'=>'contests', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/caption-battles/*", array('controller'=>'contests', 'action' => 'index', 'prefix' => 'admin', 'admin' => true));

Router::connect("/{$admin_url}/link-exchange", array('controller'=>'links', 'action' => 'index', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/link-exchange/my-links/*", array('controller'=>'links', 'action' => 'my_links', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/link-exchange/:action/*", array('controller'=>'links', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/link-exchange/*", array('controller'=>'links', 'action' => 'index', 'prefix' => 'admin', 'admin' => true));

Router::connect("/{$admin_url}/videos/my-videos/*", array('controller'=>'videos', 'action' => 'my_videos', 'prefix' => 'admin', 'admin' => true));

Router::connect("/{$admin_url}/:controller", array('action' => 'index', 'prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/:controller/:action/", array('prefix' => 'admin', 'admin' => true));
Router::connect("/{$admin_url}/:controller/:action/*", array('prefix' => 'admin', 'admin' => true));


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