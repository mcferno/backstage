<?= $this->fetch('sidebar-top'); ?>

<?php if(!isset($suppressSubnav) || $suppressSubnav !== true) : ?>
<ul class="nav nav-list">
	<?php 
		if($this->Session->check('Auth.User')) {
			switch(true) {
				case ($this->request->controller == 'users'): 
				?>
					<li class="nav-header"><?= $this->request->controller; ?></li>
					<li <?php if($this->request->action == 'admin_index') { echo 'class="active"'; } ?>><?= $this->Html->link('Index',array('action'=>'index')); ?></li>
					<li <?php if($this->request->action == 'admin_add') { echo 'class="active"'; } ?>><?= $this->Html->link('Add New',array('action'=>'add')); ?></li>
				<?	break;
				case ($this->request->controller == 'posts'): 
				?>
					<li <?php if($this->request->action == 'admin_index') { echo 'class="active"'; } ?>><?= $this->Html->link('Index',array('action'=>'index')); ?></li>
				<?	break;
				case ($this->request->controller == 'links'):
				?>
					<li class="nav-header">Link Exchange</li>
					<li <?php if($this->request->action == 'admin_index') { echo 'class="active"'; } ?>><?= $this->Html->link('View All Links',array('action'=>'index')); ?></li>
					<li <?php if($this->request->action == 'admin_my_links') { echo 'class="active"'; } ?>><?= $this->Html->link('My Links',array('action'=>'my_links')); ?></li>
				<?	break;
				case ($this->request->controller == 'videos'):
				?>
					<li class="nav-header">Videos</li>
					<li <?php if($this->request->action == 'admin_index') { echo 'class="active"'; } ?>><?= $this->Html->link('All Videos',array('action'=>'index')); ?></li>
					<li <?php if($this->request->action == 'admin_my_videos') { echo 'class="active"'; } ?>><?= $this->Html->link('My Videos',array('action'=>'my_videos')); ?></li>
				<?	break; 
				default:
				?>
					<li class="nav-header">Tools</li>
					<li <?php if($this->request->controller == 'pages' && $this->request->action == 'admin_meme_generator') { echo 'class="active"'; } ?>><?= $this->Html->link('<i class="icon-edit icon-white"></i> Meme Generator',array('controller'=>'pages','action'=>'meme_generator'),array('escape'=>false)); ?></li>
					<li <?php if($this->request->controller == 'contests') { echo 'class="active"'; } ?>><?= $this->Html->link('<i class="icon-fire icon-white"></i> Caption Battles',array('controller'=>'contests','action'=>'admin_index'),array('escape'=>false)); ?></li>
					<li><?= $this->Html->link('<i class="icon-upload icon-white"></i> <strong>Upload Image</strong>',array('controller'=>'assets','action'=>'index'),array('escape'=>false, 'class' => 'image-upload-btn')); ?></li>
					<li class="divider"></li>
					<li class="nav-header">Images</li>
					<li <?php if($this->request->controller == 'assets' && $this->request->action == 'admin_index') { echo 'class="active"'; } ?>><?= $this->Html->link('<i class="icon-home icon-white"></i> My Images',array('controller'=>'assets','action'=>'index'),array('escape'=>false)); ?></li>
					<li <?php if($this->request->controller == 'assets' && in_array($this->request->action,array('admin_user','admin_users')) && !isset($this->request->params['named']['type'])) { echo 'class="active"'; } ?>><?= $this->Html->link('<i class="icon-th icon-white"></i> All User Images',array('controller'=>'assets','action'=>'users'),array('escape'=>false)); ?></li>
					<li <?php if($this->request->controller == 'assets' && in_array($this->request->action,array('admin_user','admin_users')) && isset($this->request->params['named']['type'])) { echo 'class="active"'; } ?>><?= $this->Html->link('<i class="icon-th icon-white"></i> All Memes',array('controller'=>'assets','action'=>'users', 'type' => 'Meme'),array('escape'=>false)); ?></li>
				<?	break;
			}
		}
	?>
</ul>
<?php endif; // end of optional sidebar supression ?>

<?= $this->fetch('sidebar-bottom'); ?>