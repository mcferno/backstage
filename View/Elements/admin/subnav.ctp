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
				default:
				?>
					<li class="nav-header">Images</li>
					<li <?php if($this->request->controller == 'pages' && $this->request->action == 'admin_meme_generator') { echo 'class="active"'; } ?>><?= $this->Html->link('<i class="icon-edit icon-white"></i> Meme Generator',array('controller'=>'pages','action'=>'meme_generator'),array('escape'=>false)); ?></li>
					<li <?php if($this->request->controller == 'assets' && $this->request->action == 'admin_index') { echo 'class="active"'; } ?>><?= $this->Html->link('<i class="icon-th-large icon-white"></i> My Images',array('controller'=>'assets','action'=>'index'),array('escape'=>false)); ?></li>
					<li <?php if($this->request->controller == 'assets' && in_array($this->request->action,array('admin_user','admin_users'))) { echo 'class="active"'; } ?>><?= $this->Html->link('<i class="icon-th icon-white"></i> From All Users',array('controller'=>'assets','action'=>'users'),array('escape'=>false)); ?></li>
					<li><?= $this->Html->link('<i class="icon-upload icon-white"></i> Upload New Image',array('controller'=>'assets','action'=>'index'),array('escape'=>false, 'class' => 'image-upload-btn')); ?></li>
				<?	break;
			}
		}
	?>
</ul>