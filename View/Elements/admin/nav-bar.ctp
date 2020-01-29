<?php

$userIsLoggedIn = $this->Session->check('Auth.User') === true;
$showIncompleteSections = Configure::read('Site.showIncompleteSections') === true;

$imageSectionIsActive = $this->request->controller == 'assets';
$appsSectionIsActive = ($this->request->controller == 'pages' && $this->request->action == 'admin_meme_generator')
	|| $this->request->controller == 'contests'
	|| (!$showIncompleteSections && $this->request->controller == 'links');
$otherSectionIsActive = in_array($this->request->controller, array('links', 'posts', 'videos'));

?>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<?= $this->Html->link($siteName, array('controller' => 'users', 'action' => 'dashboard'), array('class' => 'navbar-brand')); ?>

		<?php if($userIsLoggedIn) : ?>
		<div class="status navbar-left">
			<div><a class="navbar-link" href="<?= $this->Html->url(array('controller' => 'users', 'action' => 'updates')); ?>" title="Unread Network Updates"><span class="glyphicon-flag glyphicon"></span><span class="badge badge-custom badge-<?= empty($state['new_updates']) ? 'off' : 'on'; ?> updates-count"><?= empty($state['new_updates']) ? 0 : $state['new_updates']; ?></span></a></div>
			<div><a class="navbar-link" href="<?= $this->Html->url(array('controller' => 'users', 'action' => 'group_chat')); ?>" title="Unread Chat Messages"><span class="glyphicon-envelope glyphicon"></span><span class="badge badge-custom badge-<?= empty($state['new_messages']) ? 'off' : 'on'; ?> message-count"><?= empty($state['new_messages']) ? 0 : $state['new_messages']; ?></span></a></div>
			<div><span class="glyphicon-user glyphicon" title="Online Users"></span><span class="badge badge-info online-count" title="Online Users"><?= count($onlineUsers); ?></span></div>
		</div>
		<?php endif; // authenticated ?>
	</div>

	<div class="collapse navbar-collapse">
		<ul class="nav navbar-nav">

			<?php if($userIsLoggedIn) : ?>

			<li <?= $imageSectionIsActive ? 'class="active dropdown"' : 'class="dropdown"'; ?>>
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Images <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><?= $this->Html->link('<span class="glyphicon-picture glyphicon"></span> <strong>My Images</strong>', array('controller' => 'assets', 'action' => 'index'), array('escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-text-width glyphicon"></span> Meme Templates', array('controller' => 'assets', 'action' => 'users', 'type' => 'Meme-Templates'), array('escape' => false, 'title' => 'Images without any text')); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-th glyphicon"></span> All User Images', array('controller' => 'assets', 'action' => 'admin_users'), array('escape' => false, 'title' => 'Images from all users')); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-th-large glyphicon"></span> All Memes', array('controller' => 'assets', 'action' => 'users', 'type' => 'Meme'), array('escape' => false, 'title' => 'Images with text already applied')); ?></li>
					<?php if($showIncompleteSections) : ?>
					<li><?= $this->Html->link('<span class="glyphicon-fire glyphicon"></span> Caption Battles', array('controller' => 'contests', 'action' => 'index'), array('escape' => false)); ?></li>
					<?php endif; ?>
					<li class="divider extra"></li>
					<li><?= $this->Html->link('<span class="glyphicon-camera glyphicon"></span> <strong>My Albums</strong>', array('controller' => 'assets', 'action' => 'albums', 'user' => $this->Session->read('Auth.User.id')), array('escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-book glyphicon"></span> All User Albums', array('controller' => 'assets', 'action' => 'albums'), array('escape' => false)); ?></li>


					<li class="divider extra"></li>
					<li><?= $this->Html->link('<span class="glyphicon-upload glyphicon"></span> <strong>Upload Image</strong>', array('controller' => 'assets', 'action' => 'index'), array('escape' => false, 'class' => 'image-upload-btn')); ?></li>

				</ul>
			</li>

			<li><?= $this->Html->link('Meme Generator', array('controller' => 'pages', 'action' => 'meme_generator'), array('escape' => false)); ?></li>

			<?php if(!$showIncompleteSections) : ?>
			<li <?= $appsSectionIsActive ? 'class="active dropdown"' : 'class="dropdown"'; ?>>
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Apps <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><?= $this->Html->link('<span class="glyphicon-fire glyphicon"></span> Caption Battles', array('controller' => 'contests', 'action' => 'index'), array('escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-star glyphicon"></span> Links', array('controller' => 'links', 'action' => 'index'), array('escape' => false)); ?></li>
				</ul>
			</li>

			<?php else : ?>
			<li class="dropdown <?= $otherSectionIsActive ? 'active' : ''; ?>">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" title="View other site features">Other <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><?= $this->Html->link('<span class="glyphicon-star glyphicon"></span> Links', array('controller' => 'links', 'action' => 'index'), array('escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-comment glyphicon"></span> Quotes', array('controller' => 'posts', 'action' => 'index'), array('escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-facetime-video glyphicon"></span> Videos', array('controller' => 'videos', 'action' => 'index'), array('escape' => false)); ?></li>
				</ul>
			</li>
			<?php endif; ?>

			<li <?php if($this->request->controller == 'users' && $this->request->action == 'admin_group_chat') { echo 'class="active"'; } ?>><?= $this->Html->link('Chat ', array('controller' => 'users', 'action' => 'group_chat'), array('escape' => false,'class' => 'chat-link', 'title' => 'Chat with online users')); ?></li>

			<li class="visible-xs"><a href="" class="image-upload-btn" title="Upload an image"><span class="glyphicon glyphicon-cloud-upload"></span> Upload Image</a></li>
			<li class="visible-sm visible-md visible-lg"><a href="" class="image-upload-btn" title="Upload an image"><span class="glyphicon glyphicon-cloud-upload"></span></a></li>

			<?php else: // non-authenticated ?>

			<li <?php if($this->request->controller == 'users' && $this->request->action == 'admin_dashboard') { echo 'class="active"'; } ?>><?= $this->Html->link('<span class="glyphicon-home glyphicon"></span> Home', array('controller' => 'users', 'action' => 'dashboard'), array('escape' => false)); ?></li>

			<?php endif; ?>
		</ul>

		<?php if($userIsLoggedIn) : ?>

		<ul class="nav navbar-nav navbar-right navbar-avatar">

			<li class="dropdown">
				<a href="#" class="dropdown-toggle username-dd" data-toggle="dropdown"><?= $this->Session->read('Auth.User.username');?></a>
				<ul class="dropdown-menu">
					<li><?= $this->Html->link('<span class="glyphicon-pencil glyphicon"></span> Edit Account', array('controller' => 'users', 'action' => 'account'), array('escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon-off glyphicon"></span> Logout', array('controller' => 'users', 'action' => 'logout'), array('escape' => false)); ?></li>

					<?php if(Access::hasRole('Admin')) : ?>

						<li class="divider"></li>
						<li><?= $this->Html->link('<span class="glyphicon-plus-sign glyphicon"></span> Add New User', array('controller' => 'users', 'action' => 'add'), array('escape' => false)); ?></li>
						<li><?= $this->Html->link('<span class="glyphicon-th-list glyphicon"></span> List Users', array('controller' => 'users', 'action' => 'index'), array('escape' => false)); ?></li>
						<li class="divider"></li>
						<li><?= $this->Html->link('<span class="glyphicon-comment glyphicon"></span> Message Log', array('controller' => 'messages', 'action' => 'index'), array('escape' => false)); ?></li>
						<li><?= $this->Html->link('<span class="glyphicon-tags glyphicon"></span> Tag List', array('controller' => 'tags', 'action' => 'index'), array('escape' => false)); ?></li>
						<li><?= $this->Html->link('<span class="glyphicon-flash glyphicon"></span> Social Scraper', array('controller' => 'accounts', 'action' => 'index'), array('escape' => false)); ?></li>
						<li class="divider"></li>
						<li><?= $this->Html->link('<span class="glyphicon-refresh glyphicon"></span> Clear Cache', array('controller' => 'pages', 'action' => 'clear_cache'), array('escape' => false)); ?></li>

					<?php endif; // admin-only ?>
				</ul>
			</li>
		</ul>

		<?php endif; // authenticated ?>
	</div><!--/.navbar-collapse -->

<?php if($userIsLoggedIn) : ?>
<div class="slideout" style="display:none;">
	<span class="title">Online Users</span><span class="glyphicon glyphicon-user"></span><span class="names"></span>
</div>
<?php endif; // authenticated ?>
</nav>
