<?php

$this->set('suppressSubnav', true);
$this->set('contentSpan', 10);

$showIncompleteSections = Configure::read('Site.showIncompleteSections') === true;
$columnSizes = array(
	'xs' => 6,
	'sm' => 4,
	'md' => $showIncompleteSections ? 3 : 4,
);

foreach ($columnSizes as $type => $width) {
	$columnClasses[] = "col-{$type}-{$width}";
}

?>
<div class="dash">
	<div class="row">
		<div class="col-sm-8">
			<h1>dashboard</h1>
			<h4>Welcome <span class="attn"><?= $this->Session->read('Auth.User.username'); ?></span>.</h4>
		</div>
		<div class="col-sm-4">
			<?= $this->element('Flash/success', array(
					'message' => $this->Html->link('<span class="glyphicon glyphicon-flag"></span> <strong class="updates-count">'
							. $state['new_updates'] . '</strong> new user updates!',
						array('controller' => 'users', 'action' => 'updates'),
						array('escape' => false)
					),
					'class' => 'new-update-bubble ' . (empty($state['new_updates']) ? 'hide' : '')
				));
			?>
			<?= $this->element('Flash/info', array(
					'message' => $this->Html->link('<span class="glyphicon glyphicon-envelope"></span> <strong class="message-count">'
						. $state['new_messages'] . '</strong> unread messages!',
						array('controller' => 'users', 'action' => 'group_chat'),
						array('escape' => false)
					),
					'class' => 'new-message-bubble ' . (empty($state['new_messages']) ? 'hide' : '')
				));
			?>
		</div>
	</div>

	<div class="row thumbnails features">
		<div class="<?= implode(' ', $columnClasses); ?>">
			<a class="thumbnail" href="<?= $this->Html->url(array('controller' => 'pages', 'action' => 'meme_generator')); ?>">
				<?= $this->Html->image('ui/meme-generator-callout.png', array('alt' => 'Meme Generator')); ?>
			</a>
			<div class="caption"><h4 class="text-right"><span class="extra">Meme</span> Templates <span class="badge badge-custom"><?= $meme_count; ?></span></h4></div>
		</div>
		<div class="<?= implode(' ', $columnClasses); ?>">
			<a class="thumbnail" href="<?= $this->Html->url(array('controller' => 'users', 'action' => 'group_chat')); ?>">
				<?= $this->Html->image('ui/group-chat-callout.jpg', array('alt' => 'Group Chat')); ?>
			</a>
			<div class="caption"><h4 class="text-right">Users online <span class="badge badge-custom online-count" title="Online Users"><?= count($onlineUsers); ?></span></h4></div>
		</div>
		<div class="<?= implode(' ', $columnClasses); ?>">
			<a class="thumbnail" href="<?= $this->Html->url(array('controller' => 'assets', 'action' => 'index')); ?>">
				<?= $this->Html->image('ui/my-images-callout.jpg', array('alt' => 'My Images')); ?>
			</a>
			<div class="caption"><h4 class="text-right">Saved <span class="extra">Images</span> <span class="badge badge-custom"><?= $asset_count; ?></span></h4></div>
		</div>
		<div class="<?= implode(' ', $columnClasses); ?>">
			<a class="thumbnail" href="<?= $this->Html->url(array('controller' => 'assets', 'action' => 'albums', 'user' => $this->Session->read('Auth.User.id'))); ?>">
				<?= $this->Html->image('ui/my-albums-callout.jpg', array('alt' => 'My Albums')); ?>
			</a>
			<div class="caption"><h4 class="text-right">Collections <span class="badge badge-custom"><?= $album_count; ?></span></h4></div>
		</div>
		<?php if ($showIncompleteSections) : ?>
		<div class="<?= implode(' ', $columnClasses); ?>">
			<a class="thumbnail" href="<?= $this->Html->url(array('controller' => 'videos', 'action' => 'index')); ?>">
				<?= $this->Html->image('ui/videos-callout.jpg', array('alt' => 'Videos')); ?>
			</a>
			<div class="caption"><h4 class="text-right">Videos <span class="badge badge-custom"><?= $videos_count; ?></span></h4></div>
		</div>
		<?php endif; ?>
		<div class="<?= implode(' ', $columnClasses); ?>">
			<a class="thumbnail" href="<?= $this->Html->url(array('controller' => 'contests', 'action' => 'index')); ?>">
				<?= $this->Html->image('ui/contest-callout.jpg', array('alt' => 'Caption Battles')); ?>
			</a>
			<div class="caption"><h4 class="text-right">Battles <span class="badge badge-custom"><?= $contest_count; ?></span></h4></div>
		</div>
		<div class="<?= implode(' ', $columnClasses); ?>">
			<a class="thumbnail" href="<?= $this->Html->url(array('controller' => 'links', 'action' => 'index')); ?>">
				<?= $this->Html->image('ui/link-exchange-callout.jpg', array('alt' => 'All Images')); ?>
			</a>
			<div class="caption"><h4 class="text-right">Links <span class="badge badge-custom"><?= $links_count; ?></span></h4></div>
		</div>
		<?php if ($showIncompleteSections) : ?>
		<div class="<?= implode(' ', $columnClasses); ?>">
			<a class="thumbnail" href="<?= $this->Html->url(array('controller' => 'posts', 'action' => 'index')); ?>">
				<?= $this->Html->image('ui/quotes-callout.jpg', array('alt' => 'Quotes')); ?>
			</a>
			<div class="caption"><h4 class="text-right">Quotes <span class="badge badge-custom"><?= $quotes_count; ?></span></h4></div>
		</div>
		<?php endif; ?>
	</div>
</div>

<div class="row">
<div class="col-md-6">


	<h3 class="cozy-lead">
		<a href="<?= $this->Html->url(array('controller' => 'users', 'action' => 'updates')); ?>">
			<?= $this->Html->image('ui/icons/system-monitor.png'); ?> Network Updates
		</a>
		<?= $this->Html->link('<span class="glyphicon glyphicon-search"></span> View All <span class="extra">Updates</span>', array('controller' => 'users', 'action' => 'updates'), array('class' => 'btn btn-inverse btn-sm pull-right', 'escape' => false)); ?>
	</h3>
	<?= $this->element('common/updates-list', array('hideSmallPreview' => true)); ?>

</div>

<div class="col-md-6">
	<h3 class="cozy-lead"><?= $this->Html->image('ui/icons/clock.png'); ?> Recent Users</h3>
	<table class="table table-striped activity boxed-table">
		<?php foreach($recent_users as $user) : ?>
		<tr>
			<td class="time extra"><?= date('M d h:i A', strtotime($user['User']['last_seen'])); ?></td>
			<td>
				<?= $user['User']['username']; ?> was last seen online.
				<div class="time"><?= $this->Time->timeAgoInWords($user['User']['last_seen'], array('end' => '+1 year', 'accuracy' => array('month' => 'month'))); ?></div>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>

</div>

<?php /*
<div class="row">
<div class="col-md-12">
	<h3 class="cozy"><?= $this->Html->image('ui/icons/newspaper.png'); ?> Site News</h3>
	<table class="table table-striped">
		<tr><td class="short-date">Dec 07</td><td>New Album feature to organize photos into sets.</td></tr>
		<tr><td class="short-date">Sep 02</td><td>Heavy mobile optimizations. All sections reworked. Overall darker theme.</td></tr>
		<tr><td class="short-date">Apr 04</td><td>Many improvements made to the organization of images on the site.</td></tr>
		<tr><td class="short-date">Mar 03</td><td>Meme Generator font has been improved for Android and iOS users</td></tr>
		<tr><td class="short-date">Feb 24</td><td>Group Chat has optional notification sounds for new messages and @mentions.</td></tr>
		<tr><td class="short-date">Feb 02</td><td>Video section launched!</td></tr>
		<tr><td class="short-date">Dec 16</td><td>Link Exchange has easier to navigate tag lists, and allows thumbnail images for each link.</td></tr>
		<tr><td class="short-date">Nov 20</td><td>New Link Exchange section allows you to save links and tag them.</td></tr>
		<tr><td class="short-date">Oct 28</td><td>Network Update notifications alert you of all user actions.</td></tr>
		<tr><td class="short-date">Oct 28</td><td>You can comment on all saved Images and Caption Battles.</td></tr>
		<tr><td class="short-date">Oct 25</td><td>Meme Generator's font-size adjustments are improved. Makes better use of image width.</td></tr>
		<tr><td class="short-date">Oct 14</td><td>New Caption Battle section added. Battle on!</td></tr>
		<tr><td class="short-date">Oct 11</td><td>Randomized set of Meme Generator images now includes uploads from all users.</td></tr>
		<tr><td class="short-date">Sep 30</td><td>Image upload via URL now available. Supported types: PNG, JPG, GIF</td></tr>
		<tr><td class="short-date">Sep 22</td><td>User sessions now persist up to a month. Login bugs fixed.</td></tr>
		<tr><td class="short-date">May 11</td><td>Images can now be uploaded and used in the Meme Generator.</td></tr>
		<tr><td class="short-date">May 11</td><td>Meme Generator images can now be saved on the server.</td></tr>
		<tr><td class="short-date">May 11</td><td>You can view other user's images, and make memes with them.</td></tr>
		<tr><td class="short-date">Apr 28</td><td>Chat is stable now, fixed a few bugs.</td></tr>
		<tr><td>2012.04.27</td><td>New dark theme, UI overhaul.</td></tr>
		<tr><td>2012.04.26</td><td>Group chat is fairly functional now. Status bar added to nav.</td></tr>
		<tr><td>2012.04.11</td><td>Memes now have text wordwrap. Bunch more images added.</td></tr>
		<tr><td>2012.04.06</td><td>Meme generator now outputs JPEG when possible (much faster).</td></tr>
		<tr><td>2012.04.05</td><td>Custom backgrounds added, more coming soon.</td></tr>
		<tr><td>2012.04.04</td><td>Meme generator now active! No custom backgrounds yet.</td></tr>
		<tr><td>2012.04.01</td><td>Fixing UI issues. Loosening username restrictions.</td></tr>
		<tr><td>2012.03.31</td><td>Admin panel launched. Meme generator being worked on.</td></tr>
	</table>
</div>
</div>
*/ ?>
