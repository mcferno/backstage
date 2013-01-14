<div class="row-fluid">
	<div class="links form span12">
	<?php echo $this->Form->create('Video'); ?>
		<h1>Add a New Video</h1>
		<p>Upload a new video, or link to an external video (Youtube &amp; Vimeo) to add it to the video collection.</p>
		<p class="alert">At the moment, uploaded videos must be encoded correctly (MP4 and WebM format) or they will not play. We hope to add a way to convert videos automatically in the future.</p>
	<?php
		echo $this->Form->input('url', array('label' => 'URL', 'class' => 'full', 'placeholder' => 'http://example.com'));
		echo $this->Form->input('title', array('label' => 'Video Title', 'class' => 'full', 'placeholder' => 'Website name, content title, or short descriptive name'));
		echo $this->Form->input('description', array('class' => 'full', 'rows' => 4, 'placeholder' => 'Describe the website or content to entice other users to check it out.'));
	?>
		<label>Tags (to group similar type links together)</label>
		<div class="clearfix">
			<?= $this->element('common/tagging', array('model' => 'Video')); ?>
		</div>
		<p class="muted"><br><i class="icon-white icon-info-sign"></i> Try to re-use existing tags when possible. You may add new tags, but don't make them too specific, the idea is to have many links per tag.</p>

		<?= $this->Form->button('<i class="icon-white icon-plus-sign"></i> Submit',array('class'=>'btn btn-large btn-primary')); ?>

	<?php echo $this->Form->end(); ?>
	</div>
</div>