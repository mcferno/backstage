<?php
	$this->set('contentSpan', 10);
?>
<div class="row-fluid">
	<div class="span2 text-right action-bar">
		<h3>Actions</h3>
		<ul class="unstyled actions">
			<li><?= $this->Html->link('<i class="icon-white icon-pencil"></i> Add a Link', array('controller' => 'links', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?></li>
			<li>
				<div class="dropdown">
					<a class="dropdown-toggle btn btn" data-toggle="dropdown" href="#"><i class="icon icon-random"></i> Sort Links by</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
							<li><?php echo $this->Paginator->sort('created', 'Date Submitted <i class="icon-white icon-time"></i>', array('direction' => 'desc', 'escape'=>false)); ?></li>
							<li><?php echo $this->Paginator->sort('title', 'Link Name <i class="icon-white icon-comment"></i>', array('escape'=>false)); ?></li>
							<li><?php echo $this->Paginator->sort('url', 'URL <i class="icon-white icon-share-alt"></i>', array('escape'=>false)); ?></li>
					</ul>
				</div>
			</li>
		</ul>
	</div>

	<div class="span10">
		<h1>Link Exchange</h1>
		<p>Got some links others need to know about? Just want to browse a collection of the best content? This is the place.</p>

		<?= $this->element('admin/pagination'); ?>

		<ul class="link-exchange unstyled striped">
		<?php foreach ($links as $link): ?>
		<li>
			<?= $this->element('../Links/_link_item', array('link' => $link)); ?>
		</li>
		<?php endforeach; ?>
		</ul>

		<?= $this->element('admin/pagination'); ?>
	</div>
</div>