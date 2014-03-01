<?php

/**
 * Public-facing URLs. Disabled by default, may be removed in the future.
 * =============================================================================
 */
if(Configure::read('Site.showPublicPages') === true) :

	// Isolate the homepage
	Router::connect('/', array('controller' => 'posts', 'action' => 'home', 'page' => 1));
	Router::connect('/', array('controller' => 'posts', 'action' => 'index', 'page' => 1)); // reverse-routing only (dupped)
	Router::connect('/posts/*', array('controller' => 'posts', 'action' => 'index'));

	// Additional features of the site
	Router::connect('/generator', array('controller' => 'pages', 'action' => 'quote_generator'));
	Router::connect('/refresh', array('controller' => 'posts', 'action' => 'refresh'));

else: // bring auth section to the front of the site

	// catch references to the old sub sections
	Router::redirect('/backstage/*', '/');

endif; // end of showPublicPages

/**
 * TOS and Privacy Statements. Required URLs for 3rd-party API usage.
 */
Router::connect('/terms', array('controller' => 'pages', 'action' => 'display', 'terms'));
Router::connect('/privacy-policy', array('controller' => 'pages', 'action' => 'display', 'privacy_policy'));

/**
 * Backstage, the user authenticated realm
 * =============================================================================
 */

// route params yeilding a base URI for the authenticated app
Configure::write('Site.backendUrl', array('controller' => 'users', 'action' => 'login'));

// attached to all authenticated user routes.
$route_default = array('prefix' => 'admin', 'admin' => true);
$app_prefix = (Configure::read('Site.showPublicPages') === true) ? '/backstage' : '';

/**
 * Helper function to generate authenticated routes. Follows the function signature
 * of Router::connect().
 */
$appRoute = function($uri, $route = array(), $params = array()) use ($app_prefix, $route_default) {
	Router::connect("{$app_prefix}{$uri}", array_merge($route_default, $route), $params);
};

/**
 * User dashboard & login
 */
$appRoute((empty($app_prefix) ? '/' : ''), Configure::read('Site.backendUrl'));
$appRoute("/dashboard", array('controller' => 'users', 'action' => 'dashboard'));
$appRoute("/dashboard/updates/*", array('controller' => 'users', 'action' => 'updates'));
$appRoute("/users/heartbeat/*", array('controller' => 'users', 'action' => 'heartbeat'));
$appRoute("/setup", array('controller' => 'users', 'action' => 'setup'));

/**
 * Image assets and various image apps
 */
$appRoute("/my-images/*", array('controller' => 'assets', 'action' => 'index'));
$appRoute("/all-images/*", array('controller' => 'assets', 'action' => 'users'));
$appRoute("/user/*", array('controller' => 'assets', 'action' => 'user'));
$appRoute("/assets/:action/*", array('controller' => 'assets'));

$appRoute("/albums/set_cover/*", array('controller' => 'albums', 'action' => 'set_cover'));
$appRoute("/albums/save/*", array('controller' => 'albums', 'action' => 'save'));
$appRoute("/albums/delete/*", array('controller' => 'albums', 'action' => 'delete'));
$appRoute("/albums/*", array('controller' => 'assets', 'action' => 'albums'));

$appRoute("/caption-battles", array('controller' => 'contests', 'action' => 'index'));
$appRoute("/caption-battles/:action/*", array('controller' => 'contests'));
$appRoute("/caption-battles/*", array('controller' => 'contests', 'action' => 'index'));

$appRoute("/meme-generator/*", array('controller' => 'pages', 'action' => 'meme_generator'));

/**
 * CMS content pages
 */
Router::redirect("{$app_prefix}/page", "{$app_prefix}/dashboard");
$appRoute("/page/:uri", array('controller' => 'pages', 'action'=> 'content'), array('uri' => '.+'));

/**
 * Social bookmarking
 */
$appRoute("/link-exchange", array('controller' => 'links', 'action' => 'index'));
$appRoute("/link-exchange/my-links/*", array('controller' => 'links', 'action' => 'my_links'));
$appRoute("/link-exchange/:action/*", array('controller' => 'links'));
$appRoute("/link-exchange/*", array('controller' => 'links', 'action' => 'index'));

/**
 * Video catalogues
 */
$appRoute("/videos", array('controller' => 'videos', 'action' => 'index'));
$appRoute("/videos/my-videos/*", array('controller' => 'videos', 'action' => 'my_videos'));
$appRoute("/videos/:action/*", array('controller' => 'videos'));

/**
 * Misc apps and sections
 */
$appRoute("/chat", array('controller' => 'users', 'action' => 'group_chat'));
$appRoute("/messages/add/*", array('controller' => 'messages', 'action' => 'add'));
$appRoute("/quotes/*", array('controller' => 'posts', 'action'=> 'index'));
$appRoute("/tags/:action/*", array('controller' => 'tags'));
$appRoute("/users/:action/*", array('controller' => 'users'));
$appRoute("/accounts/:action/*", array('controller' => 'accounts'));
$appRoute("/messages/:action/*", array('controller' => 'messages'));
$appRoute("/pages/:action", array('controller' => 'pages'));

// =============================================================================

unset($adminRoute, $route_default, $app_prefix);
