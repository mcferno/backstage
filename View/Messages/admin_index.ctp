<?php
$this->set('suppressSubnav', true);
$filter_names = array('model', 'user', 'text');
$filters = array_intersect_key($this->request->params['named'], array_flip($filter_names));
?>
<div class="posts index">
	<h2>Message Log</h2>
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
			<th><?php echo $this->Paginator->sort('model', 'Model');?></th>
			<th><?php echo $this->Paginator->sort('user_id', 'User');?></th>
			<th><?php echo $this->Paginator->sort('text','Text');?></th>
		</tr>

	<?php foreach ($messages as $message): ?>

		<tr>
			<td class="time"><?php echo h(date('Y.m.d H:i:s', strtotime($message['Message']['created']))); ?>&nbsp;</td>
			<td><?= $this->Paginator->link($message['Message']['model'], array('model' => $message['Message']['model'])); ?>&nbsp;</td>
			<td><?= $this->Paginator->link($message['User']['username'], array('user' => $message['Message']['user_id'])); ?>&nbsp;</td>
			<td>
				<?php echo $this->Form->postLink('×', array('action' => 'delete', $message['Message']['id']), array('class' => 'close'), "Are you sure you want to delete the Message by {$message['User']['username']}?"); ?>
				<?php echo $this->Text->autoLinkUrls($message['Message']['text']); ?>
			</td>
		</tr>

	<?php endforeach; ?>

	</table>
	<?= $this->element('admin/pagination',array('show_summary'=>true)); ?>
</div>