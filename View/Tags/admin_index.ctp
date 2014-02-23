<?php
$this->set('suppressSubnav', true);
$filter_names = array('model', 'user', 'text');
$filters = array_intersect_key($this->request->params['named'], array_flip($filter_names));
?>
<div class="posts index">
	<h2>Tag List</h2>
	<p class="text-right"><?= $this->Html->link('<span class="glyphicon glyphicon-plus-sign"></span> Add new Tag', array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
	<?php if(!empty($filters)) : ?>
	<h5>
		Filtered by
	<?php foreach ($filters as $key => $value) : ?>
		<?= $this->Paginator->link("{$key} ×", array($key => false), array('class' => 'badge')); ?>
	<?php endforeach; ?>
	</h5>
	<?php endif; ?>
	<br>
	<?= $this->element('admin/pagination'); ?>
	<table cellpadding="0" cellspacing="0" class="table table-striped">

		<tr>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('User.username', 'Added By');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('count', 'Usage Tally');?></th>
		</tr>

	<?php foreach ($tags as $tag): ?>

		<tr>
			<td class="time"><?php echo h(date('Y.m.d H:i:s', strtotime($tag['Tag']['created']))); ?>&nbsp;</td>
			<td><?= $this->Paginator->link($tag['User']['username'], array('user' => $tag['Tag']['user_id'])); ?>&nbsp;</td>
			<td>
				<?= $tag['Tag']['name']; ?>
			</td>
			<td>
				<?= $tag['Tag']['count']; ?>

				<?php echo $this->Form->postLink('×', array('action' => 'delete', $tag['Tag']['id']), array('class' => 'close'), "Are you sure you want to delete the Message by {$tag['User']['username']}?"); ?>
				<?= $this->Html->link('<span class="glyphicon glyphicon-pencil"></span>', array('action' => 'edit', $tag['Tag']['id']), array('escape' => false, 'class' => 'pull-right')); ?>
			</td>
		</tr>

	<?php endforeach; ?>

	</table>
	<?= $this->element('admin/pagination',array('show_summary'=>true)); ?>
</div>