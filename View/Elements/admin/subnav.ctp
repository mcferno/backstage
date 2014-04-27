<?= $this->fetch('sidebar-top'); ?>

<?php if(!isset($suppressSubnav) || $suppressSubnav !== true) : ?>
<ul class="nav nav-pills nav-stacked">
	<?php
		if($this->Session->check('Auth.User')) {
			switch(true) {
				case ($this->request->controller == 'users'):
				?>
					<li class="nav-header"><?= $this->request->controller; ?></li>
					<li <?php if($this->request->action == 'admin_index') { echo 'class="active"'; } ?>><?= $this->Html->link('User List', array('action' => 'index')); ?></li>
					<li <?php if($this->request->action == 'admin_add') { echo 'class="active"'; } ?>><?= $this->Html->link('Add New User', array('action' => 'add')); ?></li>
				<?php break;
				case ($this->request->controller == 'posts'):
				?>
					<li <?php if($this->request->action == 'admin_index') { echo 'class="active"'; } ?>><?= $this->Html->link('Index', array('action' => 'index')); ?></li>
				<?php break;
				case ($this->request->controller == 'links'):
				?>
					<li class="nav-header">Link Exchange</li>
					<li <?php if($this->request->action == 'admin_index') { echo 'class="active"'; } ?>><?= $this->Html->link('View All Links', array('action' => 'index')); ?></li>
					<li <?php if($this->request->action == 'admin_my_links') { echo 'class="active"'; } ?>><?= $this->Html->link('My Links', array('action' => 'my_links')); ?></li>
				<?php break;
				case ($this->request->controller == 'videos'):
				?>
					<li class="nav-header">Videos</li>
					<li <?php if($this->request->action == 'admin_index') { echo 'class="active"'; } ?>><?= $this->Html->link('All Videos', array('action' => 'index')); ?></li>
					<li <?php if($this->request->action == 'admin_my_videos') { echo 'class="active"'; } ?>><?= $this->Html->link('My Videos', array('action' => 'my_videos')); ?></li>
				<?php break;
				default:
				?>
					<li class="nav-header">Apps</li>
					<li <?php if($this->request->controller == 'pages' && $this->request->action == 'admin_meme_generator') { echo 'class="active"'; } ?>><?= $this->Html->link('<span class="glyphicon-edit glyphicon"></span> Meme Generator', array('controller' => 'pages', 'action' => 'meme_generator'), array('escape' => false)); ?></li>
					<li <?php if($this->request->controller == 'contests') { echo 'class="active"'; } ?>><?= $this->Html->link('<span class="glyphicon-fire glyphicon"></span> Caption Battles', array('controller' => 'contests', 'action' => 'admin_index'), array('escape' => false)); ?></li>

					<?php
						$is_asset = ($this->request->controller == 'assets');
						$is_owner_filter = (isset($this->request->params['named']['user']) && Access::isOwner($this->request->params['named']['user']));
						$is_album_filter = (isset($this->request->params['named']['album']));
						$is_personal_assets = $is_asset && $this->request->action == 'admin_index' && !isset($this->request->params['named']['album']);
						$is_personal_albums = $is_asset && (
							($this->request->action == 'admin_index' && isset($this->request->params['named']['album']))
							|| ($this->request->action == 'admin_albums' && $is_owner_filter)
						);
						$is_other_albums = ($is_asset && !$is_personal_albums &&
							($this->request->action == 'admin_albums' || $is_album_filter)
						);
						$is_assets_index = (in_array($this->request->action, array('admin_user', 'admin_users')) && !$is_album_filter);
					?>

					<li class="nav-header">Images</li>

					<li <?php if($is_personal_assets) { echo 'class="active"'; } ?>><?= $this->Html->link('<span class="glyphicon-picture glyphicon"></span> <strong>My Images</strong>', array('controller' => 'assets', 'action' => 'index'), array('escape' => false)); ?></li>
					<li <?php if($is_asset && $is_assets_index && isset($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'Meme-Templates') { echo 'class="active"'; } ?>><?= $this->Html->link('<span class="glyphicon-text-width glyphicon"></span> Meme Templates', array('controller' => 'assets', 'action' => 'users', 'type' => 'Meme-Templates'), array('escape' => false)); ?></li>
					<li <?php if($is_asset && $is_assets_index && !isset($this->request->params['named']['type'])) { echo 'class="active"'; } ?>><?= $this->Html->link('<span class="glyphicon-th glyphicon"></span> All User Images', array('controller' => 'assets', 'action' => 'users'), array('escape' => false)); ?></li>
					<li <?php if($is_asset && $is_assets_index && isset($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'Meme') { echo 'class="active"'; } ?>><?= $this->Html->link('<span class="glyphicon-th-large glyphicon"></span> All Memes', array('controller' => 'assets', 'action' => 'users', 'type' => 'Meme'), array('escape' => false)); ?></li>


					<li class="nav-header">Albums</li>

					<li <?php if($is_personal_albums) { echo 'class="active"'; } ?>><?= $this->Html->link('<span class="glyphicon-camera glyphicon"></span> <strong> My Albums</strong>', array('controller' => 'assets', 'action' => 'albums', 'user' => $this->Session->read('Auth.User.id')), array('escape' => false)); ?></li>
					<li <?php if($is_other_albums) { echo 'class="active"'; } ?>><?= $this->Html->link('<span class="glyphicon-book glyphicon"></span> <strong> All User Albums</strong>', array('controller' => 'assets', 'action' => 'albums'), array('escape' => false)); ?></li>

				<?php	break;
			}
		}
	?>
</ul>
<?php endif; // end of optional sidebar supression ?>

<?= $this->fetch('sidebar-bottom'); ?>