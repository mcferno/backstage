<?php 
	$rel_path = IMAGES.$user_dir.$asset['Asset']['filename'];
	$specs = getimagesize($rel_path);
	$filesize = filesize($rel_path);
	$this->set('contentSpan',10);

	// load cropping library if the image is not too small (crop-worthy)
	$load_cropper = isset($this->request->params['named']['crop']);
?>
<div class="row-fluid">
	<div class="span12">
		<h1>Saved Image</h1>
	</div>
</div>
	
<div class="row-fluid">
	<div class="span2 asset-view text-right action-bar">
		<ul class="unstyled">
			<li><strong><?= $asset['User']['username']; ?></strong> <i class="icon-white icon-user"></i></li>
			<li><?= $asset['Asset']['created']; ?> <i class="icon-white icon-time"></i></li>
			<li><?= $specs['mime']; ?> <i class="icon-white icon-picture"></i></li>
			<li><?= $specs[0]; ?> x <?= $specs[1]; ?> px (W x H) <i class="icon-white icon-resize-full"></i></li>
			<li><?= round($filesize / 1024.0,2); ?> KB <i class="icon-white icon-file"></i></li>
		</ul>
		<h3>Actions</h3>
		<ul class="unstyled actions">
			<?php if(in_array($asset['Asset']['type'], array('Upload', 'URLgrab', 'Crop'))) : ?>

			<li><?= $this->Html->link('<i class="icon-white icon-picture"></i> <strong>Meme</strong>',array('controller'=>'pages', 'action' => 'meme_generator', 'asset' => $asset['Asset']['id']),array('class'=>'btn btn-large btn-primary','escape'=>false, 'title' => 'Use this image in the Meme Generator')); ?></li>
			<li><?= $this->Html->link('<i class="icon-white icon-play-circle"></i> Caption Battle',array('controller'=>'pages', 'action' => 'meme_generator', 'asset' => $asset['Asset']['id']),array('class'=>'btn btn-primary contest-start','escape'=>false, 'title' => 'Start a Caption Battle with this image')); ?></li>

			<?php endif; // upload or url download ?>

			<li><?= $this->Html->link('<i class="icon icon-comment"></i> Post to Chat', array('action'=>'chat_post', $asset['Asset']['id']), array('class'=>'btn','escape'=>false, 'title' => 'Post this image directly into the Group Chat')); ?></li>

			<?php if(!$load_cropper) : ?>
			<li><?= $this->Html->link('<i class="icon icon-fullscreen"></i> Crop Image', array('action'=>'view', $asset['Asset']['id'], 'crop' => 'true'), array('class'=>'btn','escape'=>false, 'title' => 'Save a slice of this image')); ?></li>
			<?php endif; // cropper option ?>

			<?php if(Access::isOwner($asset['Asset']['user_id'])) : ?>

			<?php if($this->Session->check('Auth.User.fb_target')) : ?>
			<li><?= $this->Html->link('<i class="icon-white icon-upload"></i> Post to <strong>Facebook</strong>','#fbPostModal',array('class'=>'btn btn-success post-to-fb','escape'=>false, 'data-toggle' => 'modal', 'title' => 'Post this image to Facebook')); ?></li>
			<?php endif; // image is Facebook shareable ?>

			<li><?= $this->Html->link('<i class="icon-white icon-remove"></i> Delete Image',array('action'=>'delete',$asset['Asset']['id']),array('class'=>'btn btn-danger delete','escape'=>false, 'title' => 'Delete this image'),'Are you sure you wish to permanently delete this image?'); ?></li>

			<?php else : // someone else's image ?>

			<li><?= $this->Html->link('<i class="icon-white icon-user"></i> More from '.$asset['User']['username'],array('action'=>'user',$asset['Asset']['user_id']),array('class'=>'btn btn-info','escape'=>false, 'title' => 'View more images from ' . $asset['User']['username'])); ?></li>
			
			<?php endif; ?>

			<?php if(Access::hasRole('Admin')) : ?>

			<li>
			<p>Change Type <i class="icon-white icon-question-sign"></i></p>
			<?php 
				echo $this->Form->create('Asset', array('url' => array('action' => 'edit'), 'class' => 'asset-type-form'));
				echo $this->Form->input('id', array('value' => $asset['Asset']['id']));
				echo $this->Form->input('type', array('label' => false, 'class' => 'asset-type'));
				echo $this->Form->submit('Save', array('class' => 'asset-type-submit btn-small btn-primary'));
				echo $this->Form->end();
			?>
			</li>

			<?php endif; ?>
		</ul>
	</div>
	<div class="span10">
		<?php if($load_cropper && isset($this->request->params['named']['crop'])) : ?>
		<p><i class="icon-white icon-info-sign"></i> Click and drag a region on this image to begin a crop.</p>
		<?php endif; // cropper tooltip ?>

		<?php if($load_cropper) { echo $this->element('common/image-cropper'); } ?>

		<p class="text-center"><?= $this->Html->image($user_dir.$asset['Asset']['filename'], array('class' => ($load_cropper) ? 'cropable' : '', 'data-image-id' => $asset['Asset']['id'])); ?></p>

		<p>
		<?php foreach($asset['Tag'] as $idx => $tag) : ?>
		<a href="<?= $this->Html->url(array('controller' => 'assets', 'action' => 'users', 'tag' => $tag['id'])); ?>"><span class="badge badge-<?= ($idx % 2 == 0) ? 'info' : 'pale'; ?>"><?= $tag['name']; ?></span></a>
		<?php endforeach; ?>
		</p>

		<p class=" text-center">Direct URL to Image<br><input type="text" class="span4 copier" value="<?= $this->Html->url('/',true) . IMAGES_URL . $user_dir . $asset['Asset']['filename']; ?>"></p>

		<h3 class="text-right"><?= $this->Html->image('ui/icons/balloon.png'); ?> Comments</h3>
		<?= $this->element('common/chat-module', array('model' => 'Asset', 'foreign_key' => $asset['Asset']['id'])); ?>

		<div class="clearfix cozy">
			<h4 class="cozy">Add or Remove Tags</h4>
			<form>
				<?= $this->element('common/tagging', array('model' => 'Asset', 'foreign_key' => $this->request->data['Asset']['id'], 'mode' => 'live')); ?>
			</form>
		</div>
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
		<?= $this->Form->input('message',array('type'=>'text','label'=>'Message to post with image (optional)', 'class' => 'span5', 'placeholder' => 'Check this shit out', 'spellcheck' => 'true')); ?>
	</div>
	<div class="modal-footer">
		<?= $this->Form->button('<i class="icon-white icon-upload"></i> Upload', array('class' => 'btn btn-primary')); ?>
		<a href="#" class="btn btn-danger" data-dismiss="modal">Cancel</a>
	</div>
	<?= $this->Form->end(); ?>
</div>
<?php endif; ?>

<?= $this->element('common/contest-start'); ?>
