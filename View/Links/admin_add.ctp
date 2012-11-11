<div class="links form">
<?php echo $this->Form->create('Link'); ?>
	<fieldset>
		<h1>Add a New Link</h1>
	<?php
		echo $this->Form->input('url', array('label' => 'URL', 'class' => 'span8', 'placeholder' => 'http://example.com'));
		echo $this->Form->input('title', array('label' => 'Link Title', 'class' => 'span8'));
		echo $this->Form->input('description', array('class' => 'span8'));
	?>
	<label>Tags</label>
	<?php
		echo $this->element('common/tagging', array('model' => 'Link'));
		echo $this->Form->button('<i class="icon-white icon-plus-sign"></i> Submit',array('class'=>'btn btn-large btn-primary')); 
	?>
	</fieldset>
<?php echo $this->Form->end(); ?>
</div>
