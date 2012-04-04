<ul class="nav nav-list">
	<li class="nav-header"><?= $this->request->controller; ?></li>
	<?php 
		if($this->Session->check('Auth.User')) {
			switch($this->request->controller) {
				case 'users': 
				?>
					<li <?php if($this->request->action == 'admin_index') { echo 'class="active"'; } ?>><?= $this->Html->link('Index',array('action'=>'index')); ?></li>
					<li <?php if($this->request->action == 'admin_add') { echo 'class="active"'; } ?>><?= $this->Html->link('Add New',array('action'=>'add')); ?></li>
				<?	break;
				case 'posts':
				?>
					<li <?php if($this->request->action == 'admin_index') { echo 'class="active"'; } ?>><?= $this->Html->link('Index',array('action'=>'index')); ?></li>
				<?	
					break;											
			}
		}
	?>
</ul>