<?php if($this->Session->check('Auth.User')) : ?>

<div class="asset-upload-popin modal" style="display:none;" role="dialog">
<div class="modal-dialog">
<div class="modal-content">

	<div class="modal-header">
		<a class="close" data-dismiss="modal">×</a>
		<h3><?= $this->Html->image('ui/icons/image-import.png'); ?> Add New Images</h3>
	</div>
	<?php
		echo $this->Form->create('Asset', array('url' => array('action' => 'upload'), 'type' => 'file'));
		if(isset($upload_album['id'])) {
			echo $this->Form->input('album_id', array('type' => 'hidden', 'value' => $upload_album['id']));
		}
	?>
	<div class="modal-body">
		<?php if(isset($upload_album['id'])) : ?>
		<div class="alert alert-info">Will be added to the album: <strong><?= $upload_album['title']; ?></strong></div>
		<?php endif; ?>
		<div class="assets form">
			<h4><?= $this->Html->image('ui/icons/computer.png'); ?> Upload an image from your device or computer</h4>
			<?= $this->Form->input('image', array('type' => 'file', 'label' => false, 'class' => 'form-sm')); ?>
			<div class="cozy"></div>
			<h4><?= $this->Html->image('ui/icons/network-cloud.png'); ?> Upload an image from a URL</h4>
			<?= $this->Form->input('url', array('type' => 'text', 'label' => false, 'class' => 'asset-url form-control', 'placeholder' => 'http://example.com/path/to/image.jpg')); ?>
		</div>
		<div class="progress progress-striped active" style="display:none;">
			<div class="progress-bar progress-bar-info bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
		</div>
	</div>
	<div class="modal-footer">
		<?= $this->Form->button('<span class="glyphicon glyphicon-upload"></span> Upload', array('class' => 'btn btn-success btn-upload', 'data-loading-text' => "<span class='glyphicon glyphicon-upload'></span> Uploading ...")); ?>
	</div>
	<?php echo $this->Form->end();?>

</div>
</div>
</div>

<?php if(!$this->request->is('mobile')): ?>
<div id="dropzone"></div>

<div id="dropzone-upload" class="modal" style="display:none;" role="dialog">
<div class="modal-dialog">
<div class="modal-content">

	<div class="modal-body">
		<a class="close" data-dismiss="modal">×</a>
		<h3><?= $this->Html->image('ui/icons/image-import.png'); ?> <span class="info"></span></h3>
		<div class="progress progress-striped active">
			<div class="progress-bar progress-bar-info bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
		</div>
	</div>
</div>
</div>
</div>

<?php endif; // desktop users ?>

<?php endif; // Authenticated users ?>