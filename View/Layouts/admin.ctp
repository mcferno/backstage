<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php if(!empty($page_title)) { echo "$page_title - "; } ?>Backstage</title>
	<meta name="description" content=""/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link rel="apple-touch-icon-precomposed" href="<?= FULL_BASE_URL . $this->Html->webroot('img/emblem/emblem-large-x144.jpg'); ?>" sizes="144x144">
	<link rel="apple-touch-icon-precomposed" href="<?= FULL_BASE_URL . $this->Html->webroot('img/emblem/emblem-large-x114.jpg'); ?>" sizes="114x114">
	<link rel="apple-touch-icon-precomposed" href="<?= FULL_BASE_URL . $this->Html->webroot('img/emblem/emblem-large-x72.jpg'); ?>" sizes="72x72">
	<link rel="apple-touch-icon-precomposed" href="<?= FULL_BASE_URL . $this->Html->webroot('img/emblem/emblem-large-x57.jpg'); ?>">
	<meta property="og:image" content="<?= FULL_BASE_URL . $this->Html->webroot('img/emblem-large.jpg'); ?>">
	<?php
		echo $this->Html->meta('icon', 'img/emblem.png');

		// base js libraries
		$scripts = array(
			'https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js',
			'https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js',
			'https://cdnjs.cloudflare.com/ajax/libs/backbone.js/0.9.10/backbone-min.js',
			'https://netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js',
			'backstage.js?t='.filemtime(JS.'backstage.js')
		);
		
		if($this->Session->check('Auth.User.id')) {
			$scripts[] = 'group-chat.js?t='.filemtime(JS.'group-chat.js');
		}
		
		echo $this->Html->script($scripts);
		
		echo $this->fetch('script');

		echo $this->Html->css(array(
			'https://netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css'
		));
		echo $this->fetch('css');
		echo $this->Html->css('backstage.css?t='.filemtime(CSS.'backstage.css'));
		
		echo $this->element('ga');
	?>
	<script>
		var AppBaseURL = <?= json_encode($this->Html->url('/',true)); ?>;
		var User = <?= $this->Site->userDetails(); ?>;
	</script>
</head>
<body class="index no-js route-<?= $this->request->controller ?> route-action-<?= strtr($this->request->action,array('_'=>'-')); ?>">
		<?= $this->element('admin/nav-bar'); ?>
		<div class="container">
			<div class="content">
				<div class="row">
					<?php
						$contentClasses = array('main', "col-md-{$contentSpan}");
						$hideSidebar = isset($suppressSubnav) && $suppressSubnav;
						if($contentSpan <= 8 || ($contentSpan <= 10 && $hideSidebar)) {
							$contentClasses[] = 'col-md-offset-' . intval((12 - $contentSpan) / 2);
						}
					?>
					<div class="<?= implode(' ', $contentClasses); ?>">
						<?= $this->Session->flash(); ?>
						<?php echo $this->fetch('content'); ?>
					</div>
					
					<?php if($contentSpan <= 10) : ?>
					<div class="col-md-2 subnav">
						<?= $this->element('admin/subnav'); ?>
						&nbsp;
					</div>
					<?php endif ; // show sidebar ?>
				</div>
			</div>
		</div> <!-- /container -->
		
		<?= $this->element('common/asset-upload'); ?>
		<?= $this->element('sql_dump'); ?>
</body>
</html>