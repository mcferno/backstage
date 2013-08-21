<?php
	$this->set('contentSpan', 10);
?>
<div class="row">
	<div class="col-md-2 text-right action-bar">
		<h3>Actions</h3>
		<ul class="unstyled actions">
			<?php if(Access::hasRole('Admin')) : ?>
			<li><?= $this->Html->link('<span class="glyphicon glyphicon-pencil"></span> Add a Video', array('controller' => 'videos', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false, 'title' => 'Upload a Video of your own')); ?></li>
			<?php endif; // admin-only ?>
			<li>
				<div class="dropdown">
					<a class="dropdown-toggle btn btn" data-toggle="dropdown" href="#" title="Change the order of the Videos list"><span class="glyphicon glyphicon-random"></span> Sort Videos</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
							<li><?php echo $this->Paginator->sort('created', 'by Date Submitted <span class="glyphicon glyphicon-time"></span>', array('direction' => 'desc', 'escape'=>false)); ?></li>
							<li><?php echo $this->Paginator->sort('title', 'by Video Name <span class="glyphicon glyphicon-comment"></span>', array('escape'=>false)); ?></li>
							<li><?php echo $this->Paginator->sort('url', 'by URL <span class="glyphicon glyphicon-share-alt"></span>', array('escape'=>false)); ?></li>
					</ul>
				</div>
			</li>
		</ul>
	</div>

	<div class="col-md-10">
		<h1><?= (!empty($sectionTitle)) ? $sectionTitle : 'Videos'; ?></h1>
	<?php if(!empty($tag['Tag'])) : ?>
		<h3 class="cozy">
			Viewing Videos in the Category: <span class="badge badge-info active-tag"><?= $tag['Tag']['name']; ?></span> 
			<?= $this->Html->link('Clear &times;', array('action' => 'index'), array('class' => 'badge badge-muted', 'escape' => false)); ?>
		</h3>
	<?php elseif (!empty($user['User'])) : ?>
		<h3 class="cozy">
			Viewing Videos submitted by: <?= $user['User']['username']; ?> 
		</h3>
	<?php else: ?>
		<p class="tall">A private collection of member videos for your viewing pleasure.</p>
	<?php endif; ?>

		<?= $this->element('admin/pagination'); ?>

		<?php if(!empty($videos)) : ?>
		<ul class="link-exchange unstyled striped">
		<?php foreach ($videos as $video): ?>
		<li>
			<?= $this->element('../Videos/_video_item', array('video' => $video)); ?>
		</li>
		<?php endforeach; ?>
		</ul>

		<?php else : // no videos ?>
		<p class="alert alert-info">No videos to display.</p>
		<?php endif; // videos ?>

		<?= $this->element('admin/pagination', array('show_summary' => true)); ?>
	</div>
</div>
<?php $this->element('common/tag-tally'); ?>

<?php if($this->request->is('mobile')) : $this->start('sidebar-bottom'); ?>
<p><span class="glyphicon glyphicon-info-sign"></span> Mobile users: keep an eye on the video sizes when you're not on WiFi</p>
<?php $this->end(); endif; // mobile only ?>