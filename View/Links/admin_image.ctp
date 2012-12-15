<div class="row-fluid">
	<div class="span12">
		<h2>Change Screenshot Image</h2>
		<div class="link-exchange link-view">
			<?= $this->element('../Links/_link_item', array('link' => $link, 'hideComments' => true)); ?>
		</div>
	</div>
</div>

<?php if(!empty($this->request->params['named']['mode']) && $this->request->params['named']['mode'] == 'crop') : ?>
<div class="row-fluid">
	<div class="span12">
		<h3>Crop this image</h3>
		<p>Please select a segment of this image to generate a thumbnail.</p>

		<?= $this->element('common/image-cropper'); ?>
		<?= $this->Html->image("{$thumbnail_path}/full/{$link['Link']['id']}.png", array('class' => 'cropable', 'data-crop-aspect' => '1', 'data-image-id' => $link['Link']['id'])); ?>
	</div>
</div>

<?php else: ?>

<div class="row-fluid">
	<div class="span12">
		<h3>Choose an image to represent this link</h3>
		<p>If an image already exists, it will be replaced with the one you provide.</p>

		<?php echo $this->Form->create('Link', array('type'=>'file')); ?>
		<fieldset>
			<div class="inset">
				<h4><?= $this->Html->image('ui/icons/computer.png'); ?> Upload an image from your device or computer</h4>
				<?= $this->Form->input('image',array('type'=>'file','label'=>'')); ?>
				<h4><?= $this->Html->image('ui/icons/network-cloud.png'); ?> Upload an image from a URL</h4>
				<?= $this->Form->input('url',array('type' => 'text', 'label' =>'', 'class' => 'asset-url', 'placeholder' => 'http://example.com/path/to/image.jpg')); ?>
			

			<?= $this->Form->button('<i class="icon-white icon-upload"></i> Upload',array('class'=>'btn btn-large btn-success')); ?>
			<?= $this->Html->link('<i class="icon icon-ban-circle"></i> Cancel', array('action' => 'view', $link['Link']['id']), array('class' => 'btn btn-large', 'escape' => false)); ?>
			</div>
		</fieldset>
		<?php echo $this->Form->end(); ?>
	</div>
</div>

<?php endif; ?>