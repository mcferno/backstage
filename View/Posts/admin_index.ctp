<?php
	$this->set('suppressSubnav', true);
	$this->set('contentSpan', 6);
?>
<div class="posts index">
	<h2>Quotes</h2>
	<p class="tall">Aggregated posts from <?= implode(', ', $types); ?></p>
	<?= $this->element('admin/pagination'); ?>
	<table cellpadding="0" cellspacing="0" class="table table-striped">
	<tr>
		<th><?php echo $this->Paginator->sort('date');?></th>
		<th class="hidden-xs"><?php echo $this->Paginator->sort('model', 'Source');?></th>
		<th><?php echo $this->Paginator->sort('body', 'Text');?></th>
	</tr>
	<?php
	foreach ($posts as $post):
		$matches = array();
		$handle = false;
		if(preg_match("/https:\/\/twitter.com\/#!\/([^\/]+?)\//", $post['Post']['permalink'], $matches) > 0) {
			$handle = $matches[1];
		}
	?>
	<tr>
		<td class="time"><?php echo h(date('Y.m.d', $post['Post']['date'])); ?>&nbsp;</td>
		<td class="hidden-xs"><?php echo h($post['Post']['model']); ?>&nbsp;</td>
		<td class="post-body">
			<?php echo $post['Post']['body']; ?>&nbsp;
			<?php if(!empty($handle)) : ?>
			<div class="source">&mdash; @<?php echo $handle; ?></div>
			<?php endif; ?>
			<?php if(!empty($post['Post']['source'])) : ?>
			<div class="source">&mdash; <?php echo $post['Post']['source']; ?></div>
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<?= $this->element('admin/pagination', array('show_summary' => true)); ?>
</div>