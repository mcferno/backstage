<?php
	$this->set('contentSpan',10);
?>	
<div class="row-fluid">
	<div class="span2 text-right">
		<h3>Contributing Users</h3>
		<ul class="unstyled actions">
			<?php foreach($contributingUsers as $user) : ?>
			<li><strong><?= $this->Html->link($user['User']['username'],array('action'=>'user', $user['User']['id']),array('escape'=>false)); ?></strong> <i class="icon-white icon-user"></i></li>
			<?php endforeach; ?>
		</ul>	
	</div>
	<div class="span10">

		<h1>Images From All Users</h1>
		<p>We have a total of <span class="badge <?= (count($images))?'badge-custom':''; ?>"><?= count($contributingUsers); ?></span> users contributing <span class="badge <?= (count((int)$this->Paginator->counter('{:count}')))?'badge-custom':''; ?>"><?= $this->Paginator->counter('{:count}'); ?></span> images.</p>
		
		<?= $this->element('admin/pagination',array('show_summary'=>true)); ?>
		
		<div class="image-list">
			<?php if(empty($images)) : ?>
			<div class="alert alert-info"><a class="close" data-dismiss="alert" href="#">&times;</a> No images saved.</div>
			<?php endif; ?>
			<ul class="row thumbnails">
			<?php foreach ($images as $image) : ?>
				<li class="span2 text-center">
					<h4><i class="icon-white icon-user"></i> <?= $this->Html->link($image['User']['username'],array('action'=>'user',$image['Asset']['user_id'])); ?></h4>
					<div class="thumbnail">
						<?= $this->Html->link($this->Html->image($user_dir . $image['Asset']['user_id'] . '/200/' . $image['Asset']['filename']),array('action'=>'view',$image['Asset']['id']),array('escape'=>false)); ?>
						<p class="date"><?= date('Y.m.d H:m:s',strtotime($image['Asset']['created'])); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
		
		<?= $this->element('admin/pagination'); ?>
	</div>
</div>