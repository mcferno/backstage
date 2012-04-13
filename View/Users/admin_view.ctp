<div class="users view">
	<h2><?php  echo __('User');?></h2>
	<table class="table table-striped">
		<tr>
			<td>id<td>
			<td><?= h($user['User']['id']); ?>&nbsp;
		</tr>
		<tr>
			<td>Username<td>
			<td><?= h($user['User']['username']); ?>&nbsp;
		</tr>
		<tr>
			<td>Created<td>
			<td><?= h($user['User']['created']); ?>&nbsp;
		</tr>
		<tr>
			<td>Modified<td>
			<td><?= h($user['User']['modified']); ?>&nbsp;
		</tr>
		<tr>
			<td>Role<td>
			<td><?= h($user['User']['role']); ?>&nbsp;
		</tr>
		<tr>
			<td>Last Login<td>
			<td><?= h($user['User']['last_login']); ?>&nbsp;
		</tr>
		<tr>
			<td>Last Seen<td>
			<td><?= h($user['User']['last_seen']); ?>&nbsp;
		</tr>
	</table>
</div>
<div class="actions">
	<h3>Actions</h3>
	<ul class="nav nav-pills">
		<li><?= $this->Html->link(__('Edit User'), array('action' => 'edit', $user['User']['id'])); ?> </li>
		<li><?= $this->Form->postLink(__('Delete User'), array('action' => 'delete', $user['User']['id']), null, __('Are you sure you want to delete # %s?', $user['User']['id'])); ?> </li>
	</ul>
</div>
