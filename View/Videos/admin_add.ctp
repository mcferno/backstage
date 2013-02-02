<div class="row-fluid">
	<div class="links form span12">
	<?php echo $this->Form->create('Video'); ?>
		<h1>Add a New Video</h1>
		<p>Upload a new video, or link to an external video (Youtube &amp; Vimeo) to add it to the video collection.</p>
		<p class="alert">At the moment, uploaded videos must be encoded correctly (MP4 and WebM format) or they will not play. We hope to add a way to convert videos automatically in the future.</p>
	<?php
		echo $this->Form->input('title', array('label' => 'Video Title', 'class' => 'full', 'placeholder' => 'Video name, or short descriptive title'));
	?>
		<div class="row-fluid">
			<div class="span4"><?= $this->Form->input('duration', array('label' => 'Duration (MM:SS)', 'type' => 'text', 'placeholder' => '05:14')); ?></div>
			<div class="span8"><?= $this->Form->input('filmed', array('label' => 'Date Filmed (approximate when unknown)', 'type' => 'date', 'dateFormat' => 'MY', 'maxYear' => date('Y') + 1,  'minYear' => date('Y') - 20)); ?></div>
		</div>
	<?= $this->Form->input('description', array('class' => 'full', 'rows' => 4, 'placeholder' => 'Describe the video content and why someone should be compelled to watch it.')); ?>
		<label>Tags (to group similar type videos together)</label>
		<div class="clearfix">
			<?= $this->element('common/tagging', array('model' => 'Video')); ?>
		</div>
		<p class="muted"><br><i class="icon-white icon-info-sign"></i> Try to re-use existing tags when possible. You may add new tags, but don't make them too specific, the idea is to have many videos per tag.</p>

		<?= $this->Form->button('<i class="icon-white icon-plus-sign"></i> Submit',array('class'=>'btn btn-large btn-primary')); ?>

	<?php echo $this->Form->end(); ?>
	</div>
</div>