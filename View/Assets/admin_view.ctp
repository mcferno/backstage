<?php
	$rel_path = IMAGES . $asset['Asset']['image-full'];
	$specs = getimagesize($rel_path);
	$filesize = filesize($rel_path);
	$this->set('contentSpan', 10);

	// load cropping library if the image is not too small (crop-worthy)
	$load_cropper = isset($this->request->params['named']['crop']);
?>

<div class="row">
	<div class="col-md-2 asset-view text-right action-bar">
		<div class="row">
			<div class="col-xs-6 col-md-12">
				<ul class="list-unstyled actions">

					<?php
						$active_contest = false;
						if(!empty($asset['ContestEntry'][0]['id'])) {
							$active_contest = $asset['ContestEntry'][0]['id'];
						} elseif(!empty($asset['Contest'][0]['id'])) {
							$active_contest = $asset['Contest'][0]['id'];
						}

						if($active_contest) : // image is involved in a caption contest entry
					?>
					<li><?= $this->Html->link('<span class="glyphicon glyphicon-play-circle"></span> <strong>Add Caption</strong>', array('controller' => 'pages', 'action' => 'meme_generator', 'contest' => $active_contest), array('class' => 'btn btn-block btn-primary', 'escape' => false, 'title' => 'View the Caption Battle this image belongs to')); ?></li>
					<?php endif; ?>

					<?php if(in_array($asset['Asset']['type'], array('Upload', 'URLgrab', 'Crop'))) : ?>

					<?php if(!$active_contest) : // avoid new memes when there's an active contest ?>
					<li><?= $this->Html->link('<span class="glyphicon glyphicon-picture"></span> <strong>Meme</strong>', array('controller' => 'pages', 'action' => 'meme_generator', 'asset' => $asset['Asset']['id']), array('class' => 'btn btn-block btn-primary', 'escape' => false, 'title' => 'Use this image in the Meme Generator')); ?></li>
					<?php endif; ?>

					<li><?= $this->Html->link('<span class="glyphicon glyphicon-play-circle"></span> ' . ($active_contest ? 'New Battle' : 'Caption Battle'), array('controller' => 'pages', 'action' => 'meme_generator', 'asset' => $asset['Asset']['id']), array('class' => 'btn btn-block btn-primary contest-start', 'escape' => false, 'title' => 'Start a Caption Battle with this image')); ?></li>

					<?php endif; // upload or url download ?>

					<?php if($active_contest) :  ?>
					<li><?= $this->Html->link('<span class="glyphicon glyphicon-fire"></span> View Battle', array('controller' => 'contests', 'action' => 'view', $active_contest), array('class' => 'btn btn-block btn-info', 'escape' => false, 'title' => 'View all images that belong to the same album.')); ?></li>
					<?php endif; ?>

					<?php
						if(!empty($asset['Asset']['album_id'])) :
							$album_action = (!empty($asset['Asset']['user_id']) && Access::isOwner($asset['Album']['user_id'])) ? 'index' : 'users';
							$album_title = $asset['Album']['title'];
					?>

					<li><?= $this->Html->link('<span class="glyphicon glyphicon-camera"></span> View Album', array('action' => $album_action, 'album' => $asset['Asset']['album_id']), array('class' => 'btn btn-block btn-info', 'escape' => false, 'title' => 'View all images that belong to the same album.')); ?></li>

					<li><?= $this->Form->postLink('<span class="glyphicon glyphicon-check"></span> Set As Cover', array('controller' => 'albums' ,'action' => 'set_cover', $asset['Asset']['album_id'], $asset['Asset']['id']), array('class' => 'btn btn-block btn-default', 'escape' => false, 'title' => 'Use this image as the cover for the album it belongs to.'), "Do you wish to use this image as the cover to the album \"{$album_title}\"?"); ?></li>

					<?php endif; // belongs to an album ?>

					<li><?= $this->Html->link('<span class="glyphicon glyphicon-comment"></span> Post to Chat', array('action' => 'chat_post', $asset['Asset']['id']), array('class' => 'btn btn-block btn-default', 'escape' => false, 'title' => 'Post this image directly into the Group Chat')); ?></li>

					<?php if(!$load_cropper) : ?>
					<li><?= $this->Html->link('<span class="glyphicon glyphicon-fullscreen"></span> Crop Image', array('action' => 'view', $asset['Asset']['id'], 'crop' => 'true'), array('class' => 'btn btn-block btn-default', 'escape' => false, 'title' => 'Save a slice of this image')); ?></li>
					<?php endif; // cropper option ?>

					<?php if($this->Session->check('Auth.User.fb_target')) : ?>
					<li><?= $this->Html->link('<span class="glyphicon glyphicon-upload"></span> Post to <strong>Facebook</strong>', '#fbPostModal', array('class' => 'btn btn-block btn-success post-to-fb', 'escape' => false, 'data-toggle' => 'modal', 'title' => 'Post this image to Facebook')); ?></li>
					<?php endif; // image is Facebook shareable ?>

					<?php if(Access::isOwner($asset['Asset']['user_id'])) : ?>

					<li><?= $this->Html->link('<span class="glyphicon glyphicon-remove"></span> Delete Image', array('action' => 'delete', $asset['Asset']['id']), array('class' => 'btn btn-block btn-xs btn-danger delete', 'escape' => false, 'title' => 'Delete this image'), 'Are you sure you wish to permanently delete this image?'); ?></li>

					<?php else : // someone else's image ?>

					<li><?= $this->Html->link('<span class="glyphicon glyphicon-user"></span> <span class="extra">More from </span>'.$asset['User']['username'], array('action' => 'user', $asset['Asset']['user_id']), array('class' => 'btn btn-block btn-info', 'escape' => false, 'title' => 'View more images from ' . $asset['User']['username'])); ?></li>

					<?php endif; ?>
				</ul>
			</div>
			<div class="col-xs-6 col-md-12">
				<ul class="list-unstyled">
					<li><strong><?= $asset['User']['username']; ?></strong> <span class="glyphicon glyphicon-user"></span></li>
					<li><?= $asset['Asset']['created']; ?> <span class="glyphicon glyphicon-time"></span></li>
					<li><?= $specs['mime']; ?> <span class="glyphicon glyphicon-picture"></span></li>
					<li><?= $specs[0]; ?> x <?= $specs[1]; ?> px (W x H) <span class="glyphicon glyphicon-resize-full"></span></li>
					<li><?= round($filesize / 1024.0,2); ?> KB <span class="glyphicon glyphicon-file"></span></li>


					<?php if(Access::hasRole('Admin')) : ?>

					<li>
					<p>Change Type <span class="glyphicon glyphicon-question-sign"></span></p>
					<?php
						echo $this->Form->create('Asset', array('url' => array('action' => 'edit'), 'class' => 'asset-type-form'));
						echo $this->Form->input('id', array('value' => $asset['Asset']['id']));
						echo $this->Form->input('type', array('label' => false, 'class' => 'asset-type form-control'));
						echo $this->Form->submit('Save', array('class' => 'asset-type-submit btn-sm btn-primary'));
						echo $this->Form->end();
					?>
					</li>

					<?php if(!empty($albums)) : ?>
					<li class="cozy-top">
					<?php
						echo $this->Form->create('Asset', array('url' => array('action' => 'edit'), 'class' => 'asset-type-form'));
						echo $this->Form->input('id', array('value' => $asset['Asset']['id']));
						echo $this->Form->input('album_id', array('empty' => ' - no album -', 'class' => 'form-control asset-choose-album', 'label' => 'Assign to album'));
						echo $this->Form->submit('Save', array('class' => 'asset-type-submit btn-sm btn-primary'));
						echo $this->Form->end();
					?>
					</li>
					<?php endif; ?>

					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-md-10">
		<?php if($load_cropper && isset($this->request->params['named']['crop'])) : ?>
		<p><span class="glyphicon glyphicon-info-sign"></span> Click and drag a region on this image to begin a crop.</p>
		<?php endif; // cropper tooltip ?>

		<?php if($load_cropper) { echo $this->element('common/image-cropper'); } ?>

		<a name="image" class="visible-xs visible-sm"></a>

<?php if(!empty($album)) : // image belongs to an album
	$body_offset = 3;
	$dupe_first = false;
?>

<div class="cozy-bottom">

	<div class="row">

	<?php if(!empty($album_images[0]) && $album_images[0]['Asset']['id'] !== $asset['Asset']['id']) : $body_offset = 0; $dupe_first = true; ?>
	<div class="col-md-3 visible-md visible-lg">
	<?php
		echo $this->Html->link(
			'<i class="glyphicon glyphicon-chevron-left"></i> ' . $this->Html->image($album_images[0]['Asset']['image-tiny']),
			array('action' => 'view', $album_images[0]['Asset']['id'], '#' => 'image'),
			array('escape' => false, 'title' => 'Previous image in this album')
		);
	?>
	</div>
	<?php else:
			$album_images[2] = $album_images[1];
		endif;
	?>
	<h3 class="col-md-6 col-md-offset-<?= $body_offset; ?> text-center">
		<?= $this->Html->link($album['Album']['title'], array('action' => $album_action, 'album' => $album['Album']['id'])); ?>
		<br>
		<small>Photo Album (<?= $album_offset+1; ?> of <?= $album['AssetCount'][0][0]['count']; ?>)</small>
	</h3>

	<?php if($dupe_first): ?>
	<div class="col-md-3 col-xs-6 col-sm-6 visible-xs visible-sm">
	<?php
		echo $this->Html->link(
			'<i class="glyphicon glyphicon-chevron-left"></i> ' . $this->Html->image($album_images[0]['Asset']['image-tiny']),
			array('action' => 'view', $album_images[0]['Asset']['id'], '#' => 'image'),
			array('escape' => false, 'title' => 'Previous image in this album')
		);
	?>
	</div>
	<?php endif; ?>

	<?php if(!empty($album_images[2])) : //next image in album order ?>
	<div class="col-md-3 col-xs-6 col-sm-6 text-right <?= $dupe_first ? '' : 'col-xs-offset-6 col-sm-offset-6 col-md-offset-0'; ?>">
	<?php
		echo $this->Html->link(
			$this->Html->image($album_images[2]['Asset']['image-tiny']) . ' <i class="glyphicon glyphicon-chevron-right"></i>',
			array('action' => 'view', $album_images[2]['Asset']['id'], '#' => 'image'),
			array('escape' => false, 'title' => 'Next image in this album')
		);
	?>
	</div>
	<?php endif; ?>

	</div><!-- /row -->

</div>

<?php endif; // image belongs to an album ?>

		<p class="text-center"><?= $this->Html->image($asset['Asset']['image-full'], array('class' => ($load_cropper) ? 'cropable' : '', 'data-image-id' => $asset['Asset']['id'])); ?></p>

		<p class="image-tags text-right">
		<?php foreach($asset['Tag'] as $idx => $tag) : ?>
		<a href="<?= $this->Html->url(array('controller' => 'assets', 'action' => 'users', 'tag' => $tag['id'])); ?>"><span class="badge badge-<?= ($idx % 2 == 0) ? 'info' : 'pale'; ?>"><?= $tag['name']; ?></span></a>
		<?php endforeach; ?>
		</p>

		<p class="text-center">
			Direct URL to Image
		</p>
		<div class="row">
			<form class="col-md-6 col-md-offset-3">
				<input type="text" class="copier form-control" value="<?= $this->Html->url('/', true) . IMAGES_URL . $asset['Asset']['image-full']; ?>">
			</form>
		</div>

		<h3 class="text-right clearfix"><?= $this->Html->image('ui/icons/balloon.png'); ?> Comments</h3>
		<?= $this->element('common/chat-module', array('model' => 'Asset', 'foreign_key' => $asset['Asset']['id'])); ?>

		<?php if(!$this->request->is('mobile')) : ?>
		<div class="clearfix cozy">
			<h4 class="cozy text-right">Add or Remove Tags</h4>
			<form>
				<?= $this->element('common/tagging', array('model' => 'Asset', 'foreign_key' => $this->request->data['Asset']['id'], 'mode' => 'live')); ?>
			</form>
		</div>
		<?php endif; // desktop only ?>
	</div>
</div>

<?php if($this->Session->check('Auth.User.fb_target')) : ?>
<div class="modal" id="fbPostModal" style="display:none;" role="dialog">
<div class="modal-dialog">
<div class="modal-content">

	<div class="modal-header">
		<button class="close" data-dismiss="modal">×</button>
		<h3 class="fb">Post to Facebook Group</h3>
	</div>
	<?= $this->Form->create('Asset', array('url' => array('action' => 'post', $asset['Asset']['id']),'type' => 'get')); ?>
	<div class="modal-body">
		<h4>Do you wish to upload this image and post it to the Facebook group?</h4>
		<p>The post will be private, and only viewable by the members of the group.</p>
		<?php if(!empty($asset['Asset']['fb_id'])) : ?>
		<p class="alert alert-warning">This image has been previously posted.</p>
		<?php endif; ?>
		<br>
		<?= $this->Form->input('message', array('type' => 'text', 'label' => 'Message to post with image (optional)', 'class' => 'form-control', 'placeholder' => 'Check this shit out', 'spellcheck' => 'true')); ?>
	</div>
	<div class="modal-footer">
		<?= $this->Form->button('<span class="glyphicon glyphicon-upload"></span> Upload', array('class' => 'btn btn-primary')); ?>
		<a href="#" class="btn btn-danger" data-dismiss="modal">Cancel</a>
	</div>
	<?= $this->Form->end(); ?>

</div>
</div>
</div>
<?php endif; ?>

<?= $this->element('common/contest-start'); ?>
