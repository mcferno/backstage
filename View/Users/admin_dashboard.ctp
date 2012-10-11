<?php $this->set('suppressSubnav', true); ?>
<div class="hero-unit">
	<h1>dashboard</h1>
	<p>Welcome <?= $this->Session->read('Auth.User.username'); ?>.</p>
	<p>Get started on your choice of site features</p>
	
	<ul class="row thumbnails features">
		<li class="span3">
			<div class="thumbnail">
				<a href="<?= $this->Html->url(array('controller'=>'pages','action'=>'meme_generator')); ?>">
					<?= $this->Html->image('ui/meme-generator-callout.png',array('alt'=>'Meme Generator')); ?>
				</a>
				<h4 class="text-right">Total Images <span class="badge badge-custom"><?= $meme_count; ?></span></h4>
				<h5 class="text-right">app version 0.6</h5>
			</div>
		</li>
		<li class="span3">
			<div class="thumbnail">
				<a href="<?= $this->Html->url(array('controller'=>'assets','action'=>'index')); ?>">
					<?= $this->Html->image('ui/my-images-callout.jpg',array('alt'=>'My Images')); ?>
				</a>
				<h4 class="text-right">Saved Goodies <span class="badge badge-custom"><?= $asset_count; ?></span></h4>
				<h5 class="text-right">app version 0.3</h5>
			</div>
		</li>
		<li class="span3">
			<div class="thumbnail">
				<a href="<?= $this->Html->url(array('controller'=>'users','action'=>'group_chat')); ?>">
					<?= $this->Html->image('ui/group-chat-callout.jpg',array('alt'=>'Group Chat')); ?>
				</a>
				<h4 class="text-right">Chat with other online users</h4>
				<h5 class="text-right">app version 0.7</h5>
			</div>
		</li>
		<li class="span3">
			<div class="thumbnail">
				<a href="<?= $this->Html->url(array('controller'=>'assets','action'=>'users')); ?>">
					<?= $this->Html->image('ui/all-images-callout.jpg',array('alt'=>'All Images')); ?>
				</a>
				<h4 class="text-right">Images added by all users <span class="badge badge-custom"><?= $asset_count_all; ?></span></h4>
				<h5 class="text-right">&nbsp;</h5>
			</div>
		</li>
		<li class="span3">
			<div class="thumbnail">
				<a href="<?= $this->Html->url(array('controller'=>'posts','action'=>'index')); ?>">
					<?= $this->Html->image('ui/quotes-callout.jpg',array('alt'=>'Quotes')); ?>
				</a>
				<h4 class="text-right">Total Quotes <span class="badge badge-custom"><?= $quotes_count; ?></span></h4>
				<h5 class="text-right">app version 1.0</h5>
			</div>
		</li>
	</ul>
</div>

<div class="row-fluid">
<div class="span12">
	<h4 class="text-right">site updates</h4>
	<table class="table table-striped">
	<tr>
		<th>date</th>
		<th>news</th>
	</tr>
	<tr><td>2012.09.30</td><td>Image upload via URL now available. Supported types: PNG, JPG, GIF</td></tr>
	<tr><td>2012.09.22</td><td>User sessions now persist up to a month. Login bugs fixed.</td></tr>
	<tr><td>2012.05.11</td><td>Images can now be uploaded and used in the Meme Generator.</td></tr>
	<tr><td>2012.05.11</td><td>Meme Generator images can now be saved on the server.</td></tr>
	<tr><td>2012.05.11</td><td>You can view other user's images, and make memes with them.</td></tr>
	<tr><td>2012.04.28</td><td>Chat is stable now, fixed a few bugs.</td></tr>
<?php /*
	<tr><td>2012.04.27</td><td>New dark theme, UI overhaul.</td></tr>
	<tr><td>2012.04.26</td><td>Group chat is fairly functional now. Status bar added to nav.</td></tr>
	<tr><td>2012.04.11</td><td>Memes now have text wordwrap. Bunch more images added.</td></tr>
	<tr><td>2012.04.06</td><td>Meme generator now outputs JPEG when possible (much faster).</td></tr>
	<tr><td>2012.04.05</td><td>Custom backgrounds added, more coming soon.</td></tr>
	<tr><td>2012.04.04</td><td>Meme generator now active! No custom backgrounds yet.</td></tr>
	<tr><td>2012.04.01</td><td>Fixing UI issues. Loosening username restrictions.</td></tr>
	<tr><td>2012.03.31</td><td>Admin panel launched. Meme generator being worked on.</td></tr>
*/ ?>
	</table>
</div>
</div>

<?php if($this->Session->read('Auth.User.role') >= 1): ?>
<div class="row-fluid">
<div class="span12">
	<h4 class="text-right">user activity</h4>
	<table class="table table-striped">
		<tr>
			<th>time</th>
			<th>status</th>
		</tr>
		<?php foreach($recent_users as $user) : ?>
		<tr>
			<td><?= date('Y.m.d H:i:s',strtotime($user['User']['last_seen'])); ?></td>
			<td><?= $user['User']['username']; ?> was last seen online.</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>
</div>
<?php endif; ?>
