<div class="modal-header">
	<a class="close" data-dismiss="modal">×</a>
	<h3>Image Upload</h3>
</div>
<?php echo $this->Form->create('Asset',array('url'=>array('action'=>'upload'),'type'=>'file'));?>
<div class="modal-body">
	<div class="assets form">
		<fieldset>
		<h4>Please choose an image to upload</h4>
		<?= $this->Form->input('image',array('type'=>'file','label'=>'')); ?>
		<p class="alert">No support for iPhone uploading at this time.</p>
		</fieldset>
	</div>
</div>
<div class="modal-footer">
	<?= $this->Form->button('<i class="icon-white icon-upload"></i> Upload',array('class'=>'btn btn-success')); ?>
</div>
<?php echo $this->Form->end();?>