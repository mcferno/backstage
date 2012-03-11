<!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php if(!empty($page_title)) { echo "$page_title - "; } ?>Kenny Quote Machine</title>
	
	<?php if(!empty($meta_description)) : ?>
	<meta name="description" content="<?= $this->Text->truncate(strip_tags(strtr($meta_description,'"',"'")),175); ?>"/>
	<?php else : ?>
	<meta name="description" content="A collection of original jokes and quotes ranging from funny, clever, outrageous, to wildly inappropriate. Updated often with fresh laughs and nonsense."/>
	<?php endif; ?>
	
	<meta name="viewport" content="width=device-width"/>
	<?= $this->Html->meta('icon'); ?>
	<?= $this->Html->css('theme.css?t='.filemtime(CSS.'theme.css')); ?>
	
	<?php
		echo $this->Html->script(array(
			'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
			
			// cache-busting site-wide js code.
			'jquery.site.js?t='.filemtime(JS.'jquery.site.js')
		)); 
		
		echo $scripts_for_layout;

		// optionally load the Google Analytics on live site
		if(stripos($_SERVER['HTTP_HOST'],'kennyquotemachine.com') !== false) { 
			echo $this->element('ga');
		} else {
			echo $this->element('ga-empty');
		}
	?>
</head>
<body class="index no-js">
	<div class="wrap" id="wrap-main">
		<header id="header">
			<div class="row">
				<h1>
					<span class="title"><?= $this->Html->link('Kenny Quote Machine','/'); ?></span>
					<?php
						foreach($breadcrumbs as $breadcrumb) {
							echo "<span><a href=\"{$breadcrumb['url']}\">{$breadcrumb['title']}</a></span>";
						}
					?>
				</h1>
			</div>
			<?php // <p class="subtitle">featuring <a href="http://twitter.com/fakeclouds" rel="external">@fakeclouds</a></p> ?>
		</header>
		<section class="content">
		
			<?= $this->Session->flash(); ?>
			
			<?= $content_for_layout; ?>
			 
		</section><!-- .content -->
	
		<footer id="footer">
			<div class="container">
				<div class="row">
					<div class="span6 nav">
						<a href="<?= $this->Html->url('/'); ?>" title="Return to the homepage">Home</a>
						<?= $this->Html->link('— Quote Generator', array('controller'=>'pages', 'action'=>'quote_generator')); ?>
					</div>
					
					<div class="span7">
						<a href="http://starkness-theme.tumblr.com/" id="credit">Starkness Theme</a>
					</div>
				</div>
			</div>
		</footer>
	
	</div><!-- .wrap #wrap-main -->
	<?= $this->element('sql_dump'); ?>
</body>
</html>