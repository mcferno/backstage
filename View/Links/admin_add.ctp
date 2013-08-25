<div class="row">
	<div class="links form span12">
	<?php echo $this->Form->create('Link'); ?>
		<h1>Add a New Link</h1>
		<p>Please be sure that the links you submit have long-running relevance for other users, and remember that quality trumps quantity. Avoid links which can very easily be found by other means.</p>
	<?php
		echo $this->Form->input('url', array('label' => 'URL', 'class' => 'form-control', 'placeholder' => 'http://example.com'));
		echo $this->Form->input('title', array('label' => 'Link Title', 'class' => 'form-control', 'placeholder' => 'Website name, content title, or short descriptive name'));
		echo $this->Form->input('description', array('class' => 'form-control', 'rows' => 4, 'placeholder' => 'Describe the website or content to entice other users to check it out.'));
	?>
		<label>Tags (to group similar type links together)</label>
		<div class="clearfix">
			<?= $this->element('common/tagging', array('model' => 'Link')); ?>
		</div>
		<p class="muted"><br><span class="glyphicon glyphicon-info-sign"></span> Try to re-use existing tags when possible. You may add new tags, but don't make them too specific, the idea is to have many links per tag.</p>

		<?= $this->Form->button('<span class="glyphicon glyphicon-plus-sign"></span> Submit',array('class'=>'btn btn-large btn-primary')); ?>

	<?php echo $this->Form->end(); ?>
	</div>
</div>