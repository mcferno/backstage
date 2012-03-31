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
	<?= $this->Html->meta('icon'); ?>
	<?= $this->Html->css('bootstrap.min.css?t='.filemtime(CSS.'bootstrap.min.css')); ?>
	<?= $this->Html->css('bootstrap-responsive.min.css?t='.filemtime(CSS.'bootstrap-responsive.min.css')); ?>
	<?= $this->Html->css('admin.css?t='.filemtime(CSS.'admin.css')); ?>
	
	<?php
		echo $this->Html->script(array(
			'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
			'bootstrap.min',
			'jquery.site.js?t='.filemtime(JS.'jquery.site.js'),
		)); 
		
		echo $scripts_for_layout;
	?>
</head>
<body class="index no-js route-<?= $this->request->controller ?> route-action-<?= strtr($this->request->action,array('_'=>'-')); ?>">
		<?= $this->element('Admin/nav-bar'); ?>
		<div class="container">
			<div class="content">
				<div class="row">
					<div class="span2">
						<ul class="nav nav-list">
							<li class="nav-header"><?= $this->request->controller; ?></li>
							<?php 
								if($this->Session->check('Auth.User')) {
									switch($this->request->controller) {
										case 'users': 
											if($this->request->action == 'admin_dashboard') {
												break;
											}
										?>
											<li <?php if($this->request->action == 'admin_index') { echo 'class="active"'; } ?>><?= $this->Html->link('Index',array('action'=>'index')); ?></li>
											<li <?php if($this->request->action == 'admin_add') { echo 'class="active"'; } ?>><?= $this->Html->link('Add New',array('action'=>'add')); ?></li>
										<?	break;
									}
								}
							?>
						</ul>
					</div>
					<div class="span10">
						<?= $this->Session->flash(); ?>
						<?php echo $this->fetch('content'); ?>	
					</div>
				</div>
			</div>
			<hr>
			<footer>
				<p>build v1.0</p>
			</footer>
		</div> <!-- /container -->
	<?= $this->element('sql_dump'); ?>
</body>
</html>