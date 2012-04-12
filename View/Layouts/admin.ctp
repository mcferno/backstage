<!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php if(!empty($page_title)) { echo "$page_title - "; } ?>KQM Backstage</title>
	
	<?php if(!empty($meta_description)) : ?>
	<meta name="description" content="<?= $this->Text->truncate(strip_tags(strtr($meta_description,'"',"'")),175); ?>"/>
	<?php else : ?>
	<meta name="description" content="A collection of original jokes and quotes ranging from funny, clever, outrageous, to wildly inappropriate. Updated often with fresh laughs and nonsense."/>
	<?php endif; ?>
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">	
	<?php
		echo $this->Html->meta('icon');
		
		echo $this->Html->script(array(
			'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
			'http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.3.3/underscore-min.js',
			'http://cdnjs.cloudflare.com/ajax/libs/backbone.js/0.9.2/backbone-min.js',			
			'bootstrap.min',
			'jquery.site.js?t='.filemtime(JS.'jquery.site.js'),
			'group-chat.js?t='.filemtime(JS.'group-chat.js')
		)); 
		
		echo $this->fetch('script');
		
		echo $this->Html->css(array(
			'bootstrap.min.css?t='.filemtime(CSS.'bootstrap.min.css'),
			'bootstrap-responsive.min.css?t='.filemtime(CSS.'bootstrap-responsive.min.css'),
			'admin.css?t='.filemtime(CSS.'admin.css')
		));
		
		echo $this->fetch('css');
		
		// grid width in units for main content block
		$contentSpan = 12;
	?>
</head>
<body class="index no-js route-<?= $this->request->controller ?> route-action-<?= strtr($this->request->action,array('_'=>'-')); ?>">
		<?= $this->element('admin/nav-bar'); ?>
		<div class="container-fluid">
			<div class="content">
				<div class="row-fluid">
					<?php if (!isset($suppressSubnav) || $suppressSubnav !== true): $contentSpan = 10; ?>
					
					<div class="span2 subnav">
						<?= $this->element('admin/subnav'); ?>
					</div>
					
					<?php endif; ?>
					
					<div class="span<?= $contentSpan; ?> main">
						<?= $this->Session->flash(); ?>
						<?php echo $this->fetch('content'); ?>
					</div>
				</div>
			</div>
			<!--
			<hr>
			<footer>
				<p></p>
			</footer>
			-->
		</div> <!-- /container -->
	<?= $this->element('sql_dump'); ?>
</body>
</html>