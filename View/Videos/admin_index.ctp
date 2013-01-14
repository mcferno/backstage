<?php
	$this->set('contentSpan', 10);
?>
<div class="row-fluid">
	<div class="span2 text-right action-bar">
		<h3>Actions</h3>
		<ul class="unstyled actions">
			<li><?= $this->Html->link('<i class="icon-white icon-pencil"></i> Add a Video', array('controller' => 'videos', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?></li>
			<li>
				<div class="dropdown">
					<a class="dropdown-toggle btn btn" data-toggle="dropdown" href="#"><i class="icon icon-random"></i> Sort Videos by</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
							<li><?php echo $this->Paginator->sort('created', 'Date Submitted <i class="icon-white icon-time"></i>', array('direction' => 'desc', 'escape'=>false)); ?></li>
							<li><?php echo $this->Paginator->sort('title', 'Video Name <i class="icon-white icon-comment"></i>', array('escape'=>false)); ?></li>
							<li><?php echo $this->Paginator->sort('url', 'URL <i class="icon-white icon-share-alt"></i>', array('escape'=>false)); ?></li>
					</ul>
				</div>
			</li>
		</ul>
	</div>

	<div class="span10">
		<h1><?= (!empty($sectionTitle)) ? $sectionTitle : 'Videos'; ?></h1>
	<?php if(!empty($tag['Tag'])) : ?>
		<h3>
			Viewing Videos in the Category: <span class="badge badge-info active-tag"><?= $tag['Tag']['name']; ?></span> 
			<?= $this->Html->link('Clear &times;', array('action' => 'index'), array('class' => 'badge badge-muted', 'escape' => false)); ?>
		</h3>
	<?php elseif (!empty($user['User'])) : ?>
		<h3>
			Viewing Videos submitted by: <?= $user['User']['username']; ?> 
		</h3>
	<?php else: ?>
		<p>A private collection of member videos for your viewing pleasure.</p>
	<?php endif; ?>
		<br>

		<?= $this->element('admin/pagination'); ?>

		<ul class="link-exchange unstyled striped">
		<?php foreach ($videos as $video): ?>
		<li>
			<?= $this->element('../Videos/_video_item', array('video' => $video)); ?>
		</li>
		<?php endforeach; ?>
		</ul>

		<?= $this->element('admin/pagination'); ?>
	</div>
</div>
<?php $this->element('common/tag-tally'); ?>