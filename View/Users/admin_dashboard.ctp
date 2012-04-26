<?php $this->set('suppressSubnav', true); ?>
<div class="hero-unit">
	<h1>dashboard</h1>
	<p>Welcome <?= $this->Session->read('Auth.User.username'); ?>.</p>
	<p>Get started on your choice of site features:</p>
	
	<ul class="row thumbnails">
		<li class="span3">
			<div class="thumbnail">
				<a href="<?= $this->Html->url(array('controller'=>'pages','action'=>'meme_generator')); ?>">
					<?= $this->Html->image('ui/meme-generator-callout.png',array('alt'=>'')); ?>
				</a>
				<h4 class="text-right">Total Images: <span class="badge badge-custom"><?= $meme_count; ?></span></h4>
				<h5 class="text-right">app version 0.6</h5>
			</div>
		</li>
		<li class="span3">
			<div class="thumbnail">
				<a href="<?= $this->Html->url(array('controller'=>'posts','action'=>'index')); ?>">
					<?= $this->Html->image('ui/quotes-callout.jpg',array('alt'=>'')); ?>
				</a>
				<h4 class="text-right">Total Quotes: <span class="badge badge-custom"><?= $quotes_count; ?></span></h4>
				<h5 class="text-right">app version 1.0</h5>
			</div>
		</li>
		<li class="span3">
			<div class="thumbnail">
				<a href="<?= $this->Html->url(array('controller'=>'users','action'=>'group_chat')); ?>">
					<?= $this->Html->image('ui/group-chat-callout.jpg',array('alt'=>'')); ?>
				</a>
				<h4 class="text-right"><em>in development</em></h4>
				<h5 class="text-right">app version 0.1</h5>
			</div>
		</li>
	</ul>
</div>

<div class="row">
<div class="span4">
	<h4 class="text-right">site updates</h4>
	<table class="table table-striped">
	<tr>
		<th>date</th>
		<th>news</th>
	</tr>
	<tr><td>2012.04.26</td><td>Group chat is fairly functional now. Status bar added to nav.</td></tr>
	<tr><td>2012.04.11</td><td>Memes now have text wordwrap. Bunch more images added.</td></tr>
	<tr><td>2012.04.06</td><td>Meme generator now outputs JPEG when possible (much faster).</td></tr>
<?php /*
	<tr><td>2012.04.05</td><td>Custom backgrounds added, more coming soon.</td></tr>
	<tr><td>2012.04.04</td><td>Meme generator now active! No custom backgrounds yet.</td></tr>
	<tr><td>2012.04.01</td><td>Fixing UI issues. Loosening username restrictions.</td></tr>
	<tr><td>2012.03.31</td><td>Admin panel launched. Meme generator being worked on.</td></tr>
*/ ?>
	</table>
</div>

<div class="span4">
	<h4 class="text-right">user activity</h4>
	<table class="table">
		<tr>
			<th>time</th>
			<th>status</th>
		</tr>
		<?php foreach($recent_users as $user) : ?>
		<tr>
			<td><?= date('Y.m.d H:i:s',strtotime($user['User']['created'])); ?></td>
			<td><?= $user['User']['username']; ?> has joined the site.</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>
</div>