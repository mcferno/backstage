<div class="row">
	<div class="col-md-12">
		<h2>Change Screenshot Image</h2>
		<div class="link-exchange link-view">
			<?= $this->element('../Links/_link_item', array('link' => $link, 'hideComments' => true)); ?>
		</div>
	</div>
</div>

<?php
	$image = "{$thumbnail_path}/full/{$link['Link']['id']}";
	if(file_exists(IMAGES_URL . "{$image}.jpg")) {
		$image .= '.jpg';
	} elseif (file_exists(IMAGES_URL . "{$image}.png")) {
		$image .= '.png';
	} else {
		$image = false;
	}
?>

<?php if(!empty($this->request->params['named']['mode']) && $this->request->params['named']['mode'] == 'crop') : ?>
<div class="row">
	<div class="col-md-12">
		<h3>Crop this image</h3>
		<p>Please select a segment of this image to generate a thumbnail.</p>
		<?php
			// display the image if we found one, otherwise invite the user to provide one
			if($image) {
				echo $this->element('common/image-cropper');
				echo $this->Html->image($image, array('class' => 'cropable', 'data-crop-aspect' => '1', 'data-image-id' => $link['Link']['id'], 'cachebust' => true));
				echo $this->Html->link('<span class="glyphicon glyphicon-chevron-left"></span> Return to Link', array('action' => 'view', $link['Link']['id']), array('class' => 'btn btn-default', 'escape' => false));
			} else {
				echo $this->Html->link('No Image Found! Please upload an image to continue', array('action' => 'image', $link['Link']['id']));
			}
		?>
	</div>
</div>

<?php else: ?>

<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<h3 class="cozy-top">Choose an image to represent this link</h3>
		<p>If an image already exists, it will be replaced with the one you provide.</p>

		<?php echo $this->Form->create('Link', array('type' => 'file')); ?>
		<fieldset>
			<div class="inset">
				<h4><?= $this->Html->image('ui/icons/computer.png'); ?> Upload an image from your device or computer</h4>
				<?= $this->Form->input('image', array('type' => 'file', 'label' => false)); ?>
				<h4><?= $this->Html->image('ui/icons/network-cloud.png'); ?> Upload an image from a URL</h4>
				<?= $this->Form->input('url', array('type' => 'text', 'label' => false, 'class' => 'asset-url form-control', 'placeholder' => 'http://example.com/path/to/image.jpg')); ?>

			<?php
				echo $this->Form->button('<span class="glyphicon glyphicon-upload"></span> Upload', array('class' => 'btn btn-success'));
				echo '&nbsp;';
				echo $this->Html->link('<span class="glyphicon glyphicon-ban-circle"></span> Cancel', array('action' => 'view', $link['Link']['id']), array('class' => 'btn btn-default', 'escape' => false));
				if($image) {
					echo '&nbsp;';
					echo $this->Html->link('<span class="glyphicon glyphicon-pencil"></span> Re-Crop Existing', array('action' => 'image', $link['Link']['id'], 'mode' => 'crop'), array('class' => 'btn btn-info', 'escape' => false));
				}
			?>
			</div>
		</fieldset>
		<?php echo $this->Form->end(); ?>
	</div>
</div>

<?php endif; ?>