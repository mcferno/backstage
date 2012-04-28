<div class="navbar">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<?= $this->Html->link('backstage',array('controller'=>'users','action'=>'dashboard'),array('class'=>'brand')); ?>
			<?php if($this->Session->check('Auth.User')) : ?>
			<div class="status">
				<a href="<?= $this->Html->url(array('controller'=>'users','action'=>'group_chat')); ?>"><i class="icon-envelope icon-white"></i><span class="badge badge-custom badge-off message-count">0</span></a>
				<i class="icon-user icon-white"></i><span class="badge badge-info online-count">1</span>
			</div>
			<?php endif; // authenticated ?>
			<div class="nav-collapse">
				<ul class="nav">
					<li <?php if($this->request->controller == 'users' && $this->request->action == 'admin_dashboard') { echo 'class="active"'; } ?>><?= $this->Html->link('<i class="icon-home icon-white"></i> Home',array('controller'=>'users','action'=>'dashboard'),array('escape'=>false)); ?></li>
					
					<?php if($this->Session->check('Auth.User')) : ?>
					
					<li <?php if($this->request->controller == 'pages' && $this->request->action == 'admin_meme_generator') { echo 'class="active"'; } ?>><?= $this->Html->link('<i class="icon-picture icon-white"></i> Meme Generator',array('controller'=>'pages','action'=>'meme_generator'),array('escape'=>false)); ?></li>
					<li <?php if($this->request->controller == 'posts') { echo 'class="active"'; } ?>><?= $this->Html->link('<i class="icon-comment icon-white"></i> Quotes',array('controller'=>'posts','action'=>'index'),array('escape'=>false)); ?></li>
					<li <?php if($this->request->controller == 'users' && $this->request->action == 'admin_group_chat') { echo 'class="active"'; } ?>><?= $this->Html->link('<i class="icon-list icon-white"></i> Group Chat ',array('controller'=>'users','action'=>'group_chat'),array('escape'=>false,'class'=>'chat-link')); ?></li>
				
					<?php endif; // authenticated ?>
				</ul>
				
				<?php if($this->Session->check('Auth.User')) : ?>
				
				<ul class="nav pull-right">
				
					<?php if((int)$this->Session->read('Auth.User.role') >= ROLES_ADMIN) : ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><?= $this->Html->link('<i class="icon-plus-sign icon-white"></i> Add New User',array('controller'=>'users','action'=>'add'),array('escape'=>false)); ?></li>
							<li><?= $this->Html->link('<i class="icon-th-list icon-white"></i> List Users',array('controller'=>'users','action'=>'index'),array('escape'=>false)); ?></li>
							<li class="divider"></li>
							<li><?= $this->Html->link('<i class="icon-refresh icon-white"></i> Clear Cache',array('controller'=>'pages','action'=>'clear_cache'),array('escape'=>false)); ?></li>
							<li></li>
						</ul>
					</li>
					<li class="divider-vertical"></li>
					<?php endif; // admin-only ?>

					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= $this->Session->read('Auth.User.username');?> <?= $this->Html->image('emblem.png'); ?><b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><?= $this->Html->link('<i class="icon-pencil icon-white"></i> Edit Account',array('controller'=>'users','action'=>'edit',$this->Session->read('Auth.User.id')),array('escape'=>false)); ?></li>
							<li class="divider"></li>
							<li><?= $this->Html->link('<i class="icon-off icon-white"></i> Logout',array('controller'=>'users','action'=>'logout'),array('escape'=>false)); ?></li>
						</ul>
					</li>
				</ul>
					
				<?php endif; // authenticated ?>
			</div><!--/.nav-collapse -->
		</div>
	</div>
</div>
<?php if($this->Session->check('Auth.User')) : ?>
<div class="slideout alert alert-info" style="display:none;">
	<a class="close" href="#">×</a><strong>users</strong>: <span class="names"></span>
</div>
<?php endif; // authenticated ?>