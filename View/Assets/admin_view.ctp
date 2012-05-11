<?php 
	$rel_path = IMAGES.$user_dir.$asset['Asset']['filename'];
	$specs = getimagesize($rel_path);
	$filesize = filesize($rel_path);
	$this->set('contentSpan',10);
?>
<div class="row">
	<div class="span12">
		<h1>Saved Image</h1>
	</div>
</div>
	
<div class="row">
	<div class="span2 asset-view text-right">
		<ul class="unstyled">
			<li><strong><?= $asset['User']['username']; ?></strong> <i class="icon-white icon-user"></i></li>
			<li><?= $asset['Asset']['created']; ?> <i class="icon-white icon-time"></i></li>
			<li><?= $specs['mime']; ?> <i class="icon-white icon-picture"></i></li>
			<li><?= $specs[0]; ?> x <?= $specs[1]; ?> px (W x H) <i class="icon-white icon-resize-full"></i></li>
			<li><?= round($filesize / 1024.0,2); ?> KB <i class="icon-white icon-file"></i></li>
		</ul>
		<h3>Actions</h3>
		<ul class="unstyled actions">
			<?php if($this->Session->read('Auth.User.id') == $asset['Asset']['user_id']) : ?>
			<li><?= $this->Html->link('<i class="icon icon-chevron-left"></i> Return to My Images',array('action'=>'index'),array('class'=>'btn','escape'=>false)); ?></li>
			<?php if($this->Session->check('Auth.User.fb_target')) : ?>
			<li><?= $this->Html->link('<i class="icon-white icon-upload"></i> Post to Facebook',array('action'=>'post',$asset['Asset']['id']),array('class'=>'btn btn-primary','escape'=>false)); ?></li>
			<?php endif; ?>
			<li><?= $this->Html->link('<i class="icon-white icon-remove"></i> Delete Image',array('action'=>'delete',$asset['Asset']['id']),array('class'=>'btn btn-danger delete','escape'=>false),'Are you sure you wish to permanently delete this image?'); ?></li>
			<?php else : ?>
			<li><?= $this->Html->link('<i class="icon-white icon-user"></i> More from '.$asset['User']['username'],array('action'=>'user',$asset['Asset']['user_id']),array('class'=>'btn btn-info','escape'=>false)); ?></li>
			<?php endif; ?>
		</ul>	
	</div>
	<div class="span8 text-center">
		<?= $this->Html->image($user_dir.$asset['Asset']['filename']); ?>
	</div>
</div>