<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<?= $this->Html->link($siteName, array('controller' => 'users','action' => 'dashboard'), array('class' => 'navbar-brand')); ?>

		<?php if($this->Session->check('Auth.User')) : ?>
		<div class="status navbar-left">
			<div><a class="navbar-link" href="<?= $this->Html->url(array('controller' => 'users','action' => 'updates')); ?>" title="Unread Network Updates"><span class="glyphicon-flag glyphicon"></span><span class="badge badge-custom badge-off updates-count">0</span></a></div>
			<div><a class="navbar-link" href="<?= $this->Html->url(array('controller' => 'users','action' => 'group_chat')); ?>" title="Unread Chat Messages"><span class="glyphicon-envelope glyphicon"></span><span class="badge badge-custom badge-off message-count">0</span></a></div>
			<div><span class="glyphicon-user glyphicon" title="Online Users"></span><span class="badge badge-info online-count" title="Online Users"><?= count($onlineUsers); ?></span></div>
		</div>
		<?php endif; // authenticated ?>
	</div>

	<div class="collapse navbar-collapse">
		<ul class="nav navbar-nav">

			<?php if($this->Session->check('Auth.User')) : ?>

			<li <?= ($this->request->controller == 'assets') ? 'class="active dropdown"' : 'class="dropdown"'; ?>>
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon-picture glyphicon"></span> Images <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><?= $this->Html->link('<span class="glyphicon-picture glyphicon"></span> <strong>My Images</strong>', array('controller' => 'assets','action' => 'index'), array('escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-text-width glyphicon"></span> Meme Templates', array('controller' => 'assets','action' => 'users', 'type' => 'Meme-Templates'), array('escape' => false, 'title' => 'Images without any text')); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-th glyphicon"></span> All User Images', array('controller' => 'assets','action' => 'admin_users'), array('escape' => false, 'title' => 'Images from all users')); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-th-large glyphicon"></span> All Memes', array('controller' => 'assets','action' => 'users', 'type' => 'Meme'), array('escape' => false, 'title' => 'Images with text already applied')); ?></li>

					<li class="divider"></li>
					<li><?= $this->Html->link('<span class="glyphicon-camera glyphicon"></span> <strong>My Albums</strong>', array('controller' => 'assets', 'action' => 'albums', 'user' => $this->Session->read('Auth.User.id')), array('escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-book glyphicon"></span> All User Albums', array('controller' => 'assets','action' => 'albums'), array('escape' => false)); ?></li>


					<li class="divider"></li>
					<li><?= $this->Html->link('<span class="glyphicon-upload glyphicon"></span> <strong>Upload Image</strong>', array('controller' => 'assets','action' => 'index'), array('escape' => false, 'class' => 'image-upload-btn')); ?></li>

				</ul>
			</li>

			<li <?= (($this->request->controller == 'pages' && $this->request->action == 'admin_meme_generator') || $this->request->controller == 'contests') ? 'class="active dropdown"' : 'class="dropdown"'; ?>>
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon-bookmark glyphicon"></span> Apps <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><?= $this->Html->link('<span class="glyphicon-edit glyphicon"></span> Meme Generator', array('controller' => 'pages','action' => 'meme_generator'), array('escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-fire glyphicon"></span> Caption Battles', array('controller' => 'contests','action' => 'index'), array('escape' => false)); ?></li>
				</ul>
			</li>

			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" title="View other site features"><span class="glyphicon glyphicon-folder-open"></span>&nbsp; Other <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><?= $this->Html->link('<span class="glyphicon-star glyphicon"></span> Links', array('controller' => 'links','action' => 'index'), array('escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-comment glyphicon"></span> Quotes', array('controller' => 'posts','action' => 'index'), array('escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-facetime-video glyphicon"></span> Videos', array('controller' => 'videos','action' => 'index'), array('escape' => false)); ?></li>
				</ul>
			</li>

			<li <?php if($this->request->controller == 'users' && $this->request->action == 'admin_group_chat') { echo 'class="active"'; } ?>><?= $this->Html->link('<span class="glyphicon-list glyphicon"></span> Chat ', array('controller' => 'users','action' => 'group_chat'), array('escape' => false,'class' => 'chat-link', 'title' => 'Chat with online users')); ?></li>

			<li><a href="" class="image-upload-btn" title="Upload an image"><span class="glyphicon glyphicon-cloud-upload"></span></a></li>

			<?php else: // non-authenticated ?>

			<li <?php if($this->request->controller == 'users' && $this->request->action == 'admin_dashboard') { echo 'class="active"'; } ?>><?= $this->Html->link('<span class="glyphicon-home glyphicon"></span> Home', array('controller' => 'users','action' => 'dashboard'), array('escape' => false)); ?></li>

			<?php endif; ?>
		</ul>

		<?php if($this->Session->check('Auth.User')) : ?>

		<ul class="nav navbar-nav navbar-right">

			<?php if(Access::hasRole('Admin')) : ?>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><?= $this->Html->link('<span class="glyphicon-plus-sign glyphicon"></span> Add New User', array('controller' => 'users','action' => 'add'), array('escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-th-list glyphicon"></span> List Users', array('controller' => 'users','action' => 'index'), array('escape' => false)); ?></li>
					<li class="divider"></li>
					<li><?= $this->Html->link('<span class="glyphicon-comment glyphicon"></span> Message Log', array('controller' => 'messages', 'action' => 'index'), array('escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-tags glyphicon"></span> Tag List', array('controller' => 'tags', 'action' => 'index'), array('escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-flash glyphicon"></span> Social Scraper', array('controller' => 'accounts', 'action' => 'index'), array('escape' => false)); ?></li>
					<li class="divider"></li>
					<li><?= $this->Html->link('<span class="glyphicon-refresh glyphicon"></span> Clear Cache', array('controller' => 'pages','action' => 'clear_cache'), array('escape' => false)); ?></li>
					<li></li>
				</ul>
			</li>
			<li class="divider-vertical"></li>
			<?php endif; // admin-only ?>

			<li class="dropdown">
				<a href="#" class="dropdown-toggle username-dd" data-toggle="dropdown"><?= $this->Session->read('Auth.User.username');?></a>
				<ul class="dropdown-menu">
					<li><?= $this->Html->link('<span class="glyphicon-pencil glyphicon"></span> Edit Account', array('controller' => 'users','action' => 'edit',$this->Session->read('Auth.User.id')), array('escape' => false)); ?></li>
					<li class="divider"></li>
					<li><?= $this->Html->link('<span class="glyphicon-off glyphicon"></span> Logout', array('controller' => 'users','action' => 'logout'), array('escape' => false)); ?></li>
				</ul>
			</li>
		</ul>

		<?php endif; // authenticated ?>
	</div><!--/.navbar-collapse -->

<?php if($this->Session->check('Auth.User')) : ?>
<div class="slideout" style="display:none;">
	<span class="title">Online Users</span><span class="glyphicon glyphicon-user"></span><span class="names"></span>
</div>
<?php endif; // authenticated ?>
</nav>
