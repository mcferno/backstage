<?php if($this->Session->check('Auth.User')) : ?>
<div class="asset-upload-popin modal" style="display:none;">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">×</a>
		<h3><?= $this->Html->image('ui/icons/image-import.png'); ?> Add New Images</h3>
	</div>
	<?php echo $this->Form->create('Asset',array('url'=>array('action'=>'upload'),'type'=>'file'));?>
	<div class="modal-body">
		<div class="assets form">
			<fieldset>
				<h4><?= $this->Html->image('ui/icons/computer.png'); ?> Upload an image from your device or computer</h4>
				<?= $this->Form->input('image',array('type'=>'file','label'=>'')); ?>
				<h4><?= $this->Html->image('ui/icons/network-cloud.png'); ?> Upload an image from a URL</h4>
				<?= $this->Form->input('url',array('type' => 'text', 'label' =>'', 'class' => 'asset-url', 'placeholder' => 'http://example.com/path/to/image.jpg')); ?>
			</fieldset>
		</div>
	</div>
	<div class="modal-footer">
		<?= $this->Form->button('<i class="icon-white icon-upload"></i> Upload',array('class'=>'btn btn-large btn-success')); ?>
	</div>
	<?php echo $this->Form->end();?>
</div>
<?php endif; ?>