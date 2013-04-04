<?php
	$this->set('contentSpan', 10);
?>
<div class="row-fluid">
	<div class="span2 text-right action-bar">
		<h3>Actions</h3>
		<ul class="unstyled actions">
			<li><?= $this->Html->link('<i class="icon-white icon-pencil"></i> Add a Link', array('controller' => 'links', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false, 'title' => 'Submit a Link of your own')); ?></li>
			<li>
				<div class="dropdown">
					<a class="dropdown-toggle btn btn" data-toggle="dropdown" href="#" title="Change the order of the Links list"><i class="icon icon-random"></i> Sort Links</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
							<li><?php echo $this->Paginator->sort('created', 'by Date Submitted <i class="icon-white icon-time"></i>', array('direction' => 'desc', 'escape'=>false)); ?></li>
							<li><?php echo $this->Paginator->sort('title', 'by Link Name <i class="icon-white icon-comment"></i>', array('escape'=>false)); ?></li>
							<li><?php echo $this->Paginator->sort('url', 'by URL <i class="icon-white icon-share-alt"></i>', array('escape'=>false)); ?></li>
					</ul>
				</div>
			</li>
		</ul>
	</div>

	<div class="span10">
		<h1><?= (!empty($sectionTitle)) ? $sectionTitle : 'Link Exchange'; ?></h1>
	<?php if(!empty($tag['Tag'])) : ?>
		<h3 class="cozy">
			Viewing Links in the Category: <span class="badge badge-info active-tag"><?= $tag['Tag']['name']; ?></span> 
			<?= $this->Html->link('Clear &times;', array('action' => 'index'), array('class' => 'badge badge-muted', 'escape' => false)); ?>
		</h3>
	<?php elseif (!empty($user['User'])) : ?>
		<h3 class="cozy">
			Viewing Links submitted by: <?= $user['User']['username']; ?> 
		</h3>
	<?php else: ?>
		<p class="tall">Got some links others need to know about? Just want to browse a collection of the best content? This is the place.</p>
	<?php endif; ?>

		<?= $this->element('admin/pagination'); ?>

		<ul class="link-exchange unstyled striped">
		<?php foreach ($links as $link): ?>
		<li>
			<?= $this->element('../Links/_link_item', array('link' => $link)); ?>
		</li>
		<?php endforeach; ?>
		</ul>

		<?= $this->element('admin/pagination', array('show_summary' => true)); ?>
	</div>
</div>
<?php $this->element('common/tag-tally'); ?>