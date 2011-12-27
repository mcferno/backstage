<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/HTML" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Kenny Quote Machine</title>
	<meta name="description" content="Description"/>    
	<meta name="viewport" content="width=device-width"/>
	
	<?= $this->Html->meta('icon'); ?>
	<!--<link rel="alternate" type="application/rss+xml" href="http://demo.tumblr.com/rss"/>-->
	<?= $this->Html->css('theme'); ?>
	<!--<script src="http://static.tumblr.com/8wirmq7/JXflrk46x/modernizr.2.0.6.min.js" type="text/javascript"></script>-->
	
	<?= $scripts_for_layout; ?>
</head>
<body class="index">
	<div class="wrap" id="wrap-main">
		<header id="header">
			<div class="row">
				<h1><?= $this->Html->link('Kenny Quote Machine','/'); ?></h1>
			</div>
			<?php // <p class="subtitle">featuring <a href="http://twitter.com/fakeclouds" rel="external">@fakeclouds</a></p> ?>
		</header>
		<section class="content">
		
			<?= $this->Session->flash(); ?>
			<?= $content_for_layout; ?>
			 
		</section><!-- .content -->

		<?php /*
		<nav class="browse clearfix">
			<form action="/search" method="get" id="tumblr-search">
				<input type="text" name="q" value="" id="tumblr-search-query"/>
				<input type="submit" value="Search" id="tumblr-search-submit"/>
			</form>
		</nav><!-- .browse -->
		*/ ?>
	
		<footer id="footer">
			<div class="container">
				<a href="#" title="/" title="Return to the Untitled homepage">Home</a> / <a href="#" title="/archive" title="Archives for Untitled">Archive</a> / <a href="#" title="http://demo.tumblr.com/rss" title="RSS feed for Untitled">RSS</a>
				<a href="#" title="http://starkness-theme.tumblr.com/?utm_source=tumblr&amp;utm_medium=site-credit&amp;utm_term=theme&amp;utm_content=footer&amp;utm_campaign=starkness" id="credit" title="Install the Starkness Tumblr Theme">Starkness Tumblr Theme</a>
			</div>
		</footer>
	
	</div><!-- .wrap #wrap-main -->
	<?= $this->element('sql_dump'); ?>
</body>
</html>

<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<?php /*
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo __('CakePHP: the rapid development php framework:'); ?>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');

		echo $scripts_for_layout;
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1><?php echo $this->Html->link(__('CakePHP: the rapid development php framework'), 'http://cakephp.org'); ?></h1>
		</div>
		<div id="content">

			<?php echo $this->Session->flash(); ?>

			<?php echo $content_for_layout; ?>

		</div>
		<div id="footer">
			<?php echo $this->Html->link(
					$this->Html->image('cake.power.gif', array('alt'=> __('CakePHP: the rapid development php framework'), 'border' => '0')),
					'http://www.cakephp.org/',
					array('target' => '_blank', 'escape' => false)
				);
			?>
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
*/?>