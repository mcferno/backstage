<?php
	$this->set('contentSpan',10);
?>	
<div class="row-fluid">
	<div class="span2 text-right action-bar">
		<h3>Contributing Users</h3>
		<ul class="unstyled">
			<?php foreach($contributingUsers as $user) : ?>
			<li><strong><?= $this->Html->link($user['User']['username'],array('action'=>'user', $user['User']['id']),array('escape'=>false)); ?></strong> <i class="icon-white icon-user"></i></li>
			<?php endforeach; ?>
		</ul>	
	</div>
	<div class="span10">

		<h1>Images From All Users</h1>
		<p class="tall">We have a total of <span class="badge <?= (count($images))?'badge-custom':''; ?>"><?= count($contributingUsers); ?></span> users contributing <span class="badge <?= (count((int)$this->Paginator->counter('{:count}')))?'badge-custom':''; ?>"><?= $this->Paginator->counter('{:count}'); ?></span> images.</p>
		
		<?= $this->element('admin/pagination'); ?>

		<div class="image-wall">
		<?php 
			foreach ($images as $image) {
				echo $this->Html->link($this->Html->image($user_dir . $image['Asset']['user_id'] . '/200/' . $image['Asset']['filename']),array('action'=>'view',$image['Asset']['id']),array('escape'=>false));
			} 
		?>
		</div>
		
		<?= $this->element('admin/pagination', array('show_summary' => true)); ?>
	</div>
</div>