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
			<?php if(in_array($asset['Asset']['type'], array('Upload', 'URLgrab'))) : ?>
			<li><?= $this->Html->link('<i class="icon-white icon-picture"></i> Meme This Image',array('controller'=>'pages', 'action' => 'meme_generator', 'asset' => $asset['Asset']['id']),array('class'=>'btn btn-primary','escape'=>false)); ?></li>
			<li><?= $this->Html->link('<i class="icon-white icon-play-circle"></i> Start Caption Battle',array('controller'=>'pages', 'action' => 'meme_generator', 'asset' => $asset['Asset']['id']),array('class'=>'btn btn-primary contest-start','escape'=>false)); ?></li>
			<?php endif; // upload or url download ?>
			<?php if($this->Session->read('Auth.User.id') == $asset['Asset']['user_id']) : ?>
			<li><?= $this->Html->link('<i class="icon icon-chevron-left"></i> Return to My Images',array('action'=>'index'),array('class'=>'btn','escape'=>false)); ?></li>
			<?php if($this->Session->check('Auth.User.fb_target')) : ?>
			<li><?= $this->Html->link('<i class="icon-white icon-upload"></i> Post to <strong>Facebook</strong>','#fbPostModal',array('class'=>'btn btn-success post-to-fb','escape'=>false, 'data-toggle' => 'modal')); ?></li>
			<?php endif; // user's own image ?>
			<li><?= $this->Html->link('<i class="icon-white icon-remove"></i> Delete Image',array('action'=>'delete',$asset['Asset']['id']),array('class'=>'btn btn-danger delete','escape'=>false),'Are you sure you wish to permanently delete this image?'); ?></li>
			<?php else : // someone else's image ?>
			<li><?= $this->Html->link('<i class="icon-white icon-user"></i> More from '.$asset['User']['username'],array('action'=>'user',$asset['Asset']['user_id']),array('class'=>'btn btn-info','escape'=>false)); ?></li>
			<?php endif; ?>
		</ul>	
	</div>
	<div class="span8 text-center">
		<p><?= $this->Html->image($user_dir.$asset['Asset']['filename']); ?></p>

		<p>Direct URL to Image<br><input type="text" class="span4 copier" value="<?= $this->Html->url('/',true) . IMAGES_URL . $user_dir . $asset['Asset']['filename']; ?>"></p>
	</div>
</div>

<?php if($this->Session->check('Auth.User.fb_target')) : ?>
<div class="modal" id="fbPostModal" style="display:none;">
	<div class="modal-header">
		<button class="close" data-dismiss="modal">×</button>
		<h3 class="fb">Post to Facebook</h3>
	</div>
	<?= $this->Form->create('Asset',array('url'=>array('action'=>'post',$asset['Asset']['id']),'type' => 'get')); ?>
	<div class="modal-body">
		<h4>Do you wish to upload this image and post it to the TYS group?</h4>
		<p>The post will be private, and only viewable by the members of the group.</p>
		<?php if(!empty($asset['Asset']['fb_id'])) : ?>
		<p class="alert alert-warning">This image has been previously posted.</p>
		<?php endif; ?>
		<br>
		<?= $this->Form->input('message',array('type'=>'text','label'=>'Message to post with image (optional)', 'class' => 'span5', 'placeholder' => 'Check this shit out')); ?>
	</div>
	<div class="modal-footer">
		<?= $this->Form->button('<i class="icon-white icon-upload"></i> Upload', array('class' => 'btn btn-primary')); ?>
		<a href="#" class="btn btn-danger" data-dismiss="modal">Cancel</a>
	</div>
	<?= $this->Form->end(); ?>
</div>
<?php endif; ?>

<?= $this->element('common/contest-start'); ?>