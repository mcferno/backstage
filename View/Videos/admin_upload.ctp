<?php
	$video_path = "user/videos/{$videos[0]['Video']['id']}";
	$screenshot = (file_exists(IMAGES_URL . "{$video_path}.png")) ? "{$video_path}.png" : false;

	$video_sizes = array();
	if($videos[0]['Video']['mp4'] && file_exists(IMAGES_URL . "${video_path}.mp4")) {
		$video_sizes['mp4'] = filesize(IMAGES_URL . "${video_path}.mp4");
	}
	if($videos[0]['Video']['webm'] && file_exists(IMAGES_URL . "${video_path}.webm")) {
		$video_sizes['webm'] = filesize(IMAGES_URL . "${video_path}.webm");
	}
?>
<h1>Upload Video Files</h1>

<ul class="link-exchange list-unstyled striped"><li><?= $this->element('../Videos/_video_item', array('video' => $videos[0], 'hideComments' => true)); ?></li></ul>

<div class="row">
	<div class="links form span12">
	<?php echo $this->Form->create('Video', array('type' => 'file', 'url' => array('action' => 'upload', $videos[0]['Video']['id']))); ?>
		<p>Currently, both the pre-encoded .mp4 and .webm files must be provided in order for all users to be able to view them.</p>
		<?php if($video_sizes) : ?>
		<p class="alert alert-info">
			<?php if(isset($video_sizes['mp4'])) : ?>
			<strong>MP4</strong> file exists (<?= CakeNumber::toReadableSize($video_sizes['mp4']); ?>) &nbsp;
			<?php endif; ?>
			<?php if(isset($video_sizes['webm'])) : ?>
			<strong>WebM</strong> file exists (<?= CakeNumber::toReadableSize($video_sizes['webm']); ?>)
			<?php endif; ?>
		</p>
		<?php endif; ?>
		<?= $this->Form->input('mp4_file', array('type' => 'file', 'label' => 'MP4 video')); ?>
		<?= $this->Form->input('webm_file', array('type' => 'file', 'label' => 'WebM video')); ?>

		<p><span class="glyphicon glyphicon-info-sign"></span> Uploading may take several minutes or longer depending on your connection, and the length/quality of the video you upload. Thank you for your patience.</p>

		<?= $this->Form->button('<span class="glyphicon glyphicon-plus-sign"></span> Submit',array('class'=>'btn btn-large btn-primary')); ?><br><br>
		<?= $this->Html->link('<span class="glyphicon glyphicon-pencil"></span> Edit Video Details', array('action' => 'edit', $videos[0]['Video']['id']), array('class' => 'btn btn-default', 'escape' => false)); ?>
		<?= $this->Html->link('<span class="glyphicon glyphicon-search"></span> View Video', array('action' => 'view', $videos[0]['Video']['id']), array('class' => 'btn btn', 'escape' => false)); ?>
	<?php echo $this->Form->end(); ?>
	</div>
</div>