<?php $this->set('suppressSubnav', true); ?>
<div class="hero-unit">
	<h1>dashboard</h1>
	<p>Welcome <?= $this->Session->read('Auth.User.username'); ?>.</p>
	<p>Get started on your choice of site features:</p>
	
	<ul class="thumbnails">
		<li class="span3">
			<div class="thumbnail">
				<a href="<?= $this->Html->url(array('controller'=>'pages','action'=>'meme_generator')); ?>">
					<?= $this->Html->image('ui/meme-generator-callout.png',array('alt'=>'')); ?>
				</a>
				<h5 class="text-right">version 0.5</h5>
			</div>
		</li>
		<li class="span3">
			<div class="thumbnail">
				<a href="<?= $this->Html->url(array('controller'=>'posts','action'=>'index')); ?>">
					<?= $this->Html->image('ui/quotes-callout.jpg',array('alt'=>'')); ?>
				</a>
				<h5 class="text-right">version 1.0</h5>
			</div>
		</li>
	</ul>
</div>

<div class="row">
<div class="span6">
	<h4 class="text-right">site updates</h4>
	<table class="table table-striped">
	<tr>
		<th>date</th>
		<th>news</th>
	</tr>
	<tr><td>2012.04.04</td><td>Meme generator now active! No custom backgrounds yet.</td></tr>
	<tr><td>2012.04.01</td><td>Fixing UI issues. Loosening username restrictions.</td></tr>
	<tr><td>2012.03.31</td><td>Admin panel launched. Meme generator being worked on.</td></tr>
	</table>
</div>

<div class="span6">
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