<?php $this->set('suppressSubnav', true); ?>
<h1>Network Updates</h1>
<p>The latest action from all users.</p>

<?php if(empty($updates)) : ?>

<p class="alert alert-warning">No updates at this time, please check back soon.</p>

<?php else : ?>

<?= $this->element('admin/pagination'); ?>

<table class="table table-striped activity">
<?php foreach($updates as $update) : ?>
	<tr>
		<td class="time"><?= date('m/d H:i:s', strtotime($update['Activity']['created'])); ?></td>
		<td class="type">
			<i class="icon-white icon-<?= (isset($update['Activity']['icon'])) ? $update['Activity']['icon'] : 'time'; ?>"></i>
		</td>
		<td><?php
			if(isset($update['Activity']['link'])) {
				echo $this->Html->link($update['Activity']['phrase'], $update['Activity']['link']);
			} else {
				echo $update['Activity']['phrase'];
			}
		?></td>
	</tr>
<?php endforeach; ?>
</table>

<?= $this->element('admin/pagination'); ?>

<?php endif; ?>