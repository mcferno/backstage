<div class="hero-unit">
	<h1>dashboard</h1>
	<p>Welcome <?= $this->Session->read('Auth.User.username'); ?>.</p>
	<p>Get started on your choice of site features:</p>
	
	<ul class="thumbnails">
		<li class="span3">
			<div class="thumbnail">
				<a href="<?= $this->Html->url(array('controller'=>'users','action'=>'dashboard')); ?>">
					<img src="http://placehold.it/260x180" alt="">
					<h3>Meme Generator!</h3>
				</a>
			</div>
		</li>
	</ul>
</div>

<div class="row">
<div class="span6">
	<h4 class="text-right">newsticker</h4>
	<table class="table table-striped">
	<tr>
		<th>date</th>
		<th>news</th>
	</tr>
	<tr>
		<td>2012.03.31</td>
		<td>Admin panel launched. Meme generator being worked on.</td>
	</tr>
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