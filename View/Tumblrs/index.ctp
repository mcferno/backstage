<div class="tumblrs index">
	<h2><?php echo __('Tumblrs');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('blog_name');?></th>
			<th><?php echo $this->Paginator->sort('tumblr_id');?></th>
			<th><?php echo $this->Paginator->sort('post_url');?></th>
			<th><?php echo $this->Paginator->sort('type');?></th>
			<th><?php echo $this->Paginator->sort('timestamp');?></th>
			<th><?php echo $this->Paginator->sort('text');?></th>
			<th><?php echo $this->Paginator->sort('source');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($tumblrs as $tumblr): ?>
	<tr>
		<td><?php echo h($tumblr['Tumblr']['id']); ?>&nbsp;</td>
		<td><?php echo h($tumblr['Tumblr']['blog_name']); ?>&nbsp;</td>
		<td><?php echo h($tumblr['Tumblr']['tumblr_id']); ?>&nbsp;</td>
		<td><?php echo h($tumblr['Tumblr']['post_url']); ?>&nbsp;</td>
		<td><?php echo h($tumblr['Tumblr']['type']); ?>&nbsp;</td>
		<td><?php echo h($tumblr['Tumblr']['timestamp']); ?>&nbsp;</td>
		<td><?php echo h($tumblr['Tumblr']['text']); ?>&nbsp;</td>
		<td><?php echo h($tumblr['Tumblr']['source']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $tumblr['Tumblr']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $tumblr['Tumblr']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $tumblr['Tumblr']['id']), null, __('Are you sure you want to delete # %s?', $tumblr['Tumblr']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Tumblr'), array('action' => 'add')); ?></li>
	</ul>
</div>
