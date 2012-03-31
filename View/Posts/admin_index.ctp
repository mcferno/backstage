<div class="posts index">
	<h2>Quotes</h2>
	<p>Aggregated posts from <?= implode(', ',$types); ?></p>
	<?= $this->element('Admin/pagination'); ?>
	<table cellpadding="0" cellspacing="0" class="table table-bordered table-condensed">
	<tr>
		<th><?php echo $this->Paginator->sort('date');?></th>
		<th><?php echo $this->Paginator->sort('model','Source');?></th>
		<th><?php echo $this->Paginator->sort('body','Text');?></th>
	</tr>
	<?php
	foreach ($posts as $post): ?>
	<tr>
		<td><?php echo h(date('Y.m.d H:i:s',$post['Post']['date'])); ?>&nbsp;</td>
		<td><?php echo h($post['Post']['model']); ?>&nbsp;</td>
		<td>
			<?php echo $post['Post']['body']; ?>&nbsp;
			<?php if(!empty($post['Post']['source'])) : ?>
			<div class="source">&mdash; <?php echo $post['Post']['source']; ?></div>
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<?= $this->element('Admin/pagination',array('show_summary'=>true)); ?>
</div>