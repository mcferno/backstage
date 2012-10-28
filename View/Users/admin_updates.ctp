<?php $this->set('suppressSubnav', true); ?>
<h2><?= $this->Html->image('ui/icons/system-monitor.png'); ?> Network Updates</h2>
<p>The latest action from all users.</p>

<?php if(empty($updates)) : ?>

<p class="alert alert-warning">No updates at this time, please check back soon.</p>

<?php else : ?>

<?= $this->element('admin/pagination'); ?>

<table class="table table-striped activity">
<?php foreach($updates as $update) : ?>
	<tr>
		<td class="time extra"><?= date('M d h:i A', strtotime($update['Activity']['created'])); ?></td>
		<td class="type"><?php
			if(isset($update['Activity']['icon'])) {
				echo $this->Html->image("ui/icons/{$update['Activity']['icon']}.png");
			}
		?></td>
		<td><?php
			$message = $update['Activity']['phrase'];
			if(isset($update['Activity']['link'])) {
				$message = $this->Html->link($message, $update['Activity']['link']);
			}
			if($update['User']['username']) {
				$message = String::insert($message, array('user' => $this->Html->tag('strong', $update['User']['username'])));
			}
			echo $message;
		?>
		<br>
		<span class="time"><?= $this->Time->timeAgoInWords($update['Activity']['created'], array('end' => '+1 year','accuracy' => array('month' => 'month'))); ?></span>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?= $this->element('admin/pagination'); ?>

<?php endif; ?>