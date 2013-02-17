<?php
	$this->set('contentSpan', 10);
?>
<div class="row-fluid">
	<div class="span2 text-right action-bar">
		<h3>Actions</h3>
		<ul class="unstyled actions">
			<li><?= $this->Html->link('<i class="icon icon-pencil"></i> Edit this Link', array('controller' => 'links', 'action' => 'edit', $link['Link']['id']), array('class' => 'btn btn-small', 'escape' => false)); ?></li>
			<li><?= $this->Html->link('<i class="icon icon-picture"></i> Screenshot', array('controller' => 'links', 'action' => 'image', $link['Link']['id']), array('class' => 'btn btn-small', 'escape' => false)); ?></li>
		</ul>
	</div>
	<div class="span10">
		<div class="link-exchange link-view">
			<?= $this->element('../Links/_link_item', array('link' => $link)); ?>
		</div>

		<h3 class="text-right"><?= $this->Html->image('ui/icons/balloon.png'); ?> Comments</h3>
		<?= $this->element('common/chat-module', array('model' => 'Link', 'foreign_key' => $link['Link']['id'])); ?>
	</div>
</div>

<?php $this->element('common/tag-tally', array('action' => (Access::isOwner($link['Link']['user_id'])) ? 'my_links' : 'index'))); ?>