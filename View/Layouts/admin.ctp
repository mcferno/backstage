<!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php if(!empty($page_title)) { echo "$page_title - "; } ?>Backstage</title>
	<meta name="description" content=""/>	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta property="og:image" content="http://<?= env('HTTP_HOST').$this->Html->webroot('img/emblem-large.png'); ?>">
	<?php
		echo $this->Html->meta('icon',$this->Html->webroot('img/emblem.png'));
		
		// base js libraries
		$scripts = array(
			'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
			'http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.3.3/underscore-min.js',
			'http://cdnjs.cloudflare.com/ajax/libs/backbone.js/0.9.2/backbone-min.js',
			'bootstrap.min',
			'jquery.site.js?t='.filemtime(JS.'jquery.site.js')
		);
		
		if($this->Session->check('Auth.User.id')) {
			$scripts[] = 'group-chat.js?t='.filemtime(JS.'group-chat.js');
		}
		
		echo $this->Html->script($scripts);
		
		echo $this->fetch('script');
		
		echo $this->Html->css(array(
			'bootstrap.min.css?t='.filemtime(CSS.'bootstrap.min.css'),
			'bootstrap-responsive.min.css?t='.filemtime(CSS.'bootstrap-responsive.min.css'),
			'admin.css?t='.filemtime(CSS.'admin.css')
		));
		
		echo $this->fetch('css');
		echo $this->element('ga');
	?>
	<script>
		var AppBaseURL = <?= json_encode($this->Html->url('/',true)); ?>;
	</script>
</head>
<body class="index no-js route-<?= $this->request->controller ?> route-action-<?= strtr($this->request->action,array('_'=>'-')); ?>">
		<?= $this->element('admin/nav-bar'); ?>
		<div class="container-fluid">
			<div class="content">
				<div class="row-fluid">
					<?php if($contentSpan <= 8) : ?>
					<div class="span2">&nbsp;</div>
					<?php endif; ?>
					
					<div class="span<?= $contentSpan; ?> main">
						<?= $this->Session->flash(); ?>
						<?php echo $this->fetch('content'); ?>
					</div>
					
					<?php if (!isset($suppressSubnav) || $suppressSubnav !== true): ?>
					<div class="span2 subnav">
						<?= $this->element('admin/subnav'); ?>
					</div>
					<?php else: ?>
					<div class="span2">&nbsp;</div>
					<?php endif; ?>
				</div>
			</div>
		</div> <!-- /container -->
	<?= $this->element('sql_dump'); ?>
</body>
</html>