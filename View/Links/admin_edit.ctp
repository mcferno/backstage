<div class="links form">
<?php echo $this->Form->create('Link'); ?>
	<fieldset>
		<h1>Edit Link</h1>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('user_id', array('type' => 'hidden'));
		echo $this->Form->input('url', array('label' => 'URL', 'class' => 'span6', 'placeholder' => 'http://example.com'));
		echo $this->Form->input('title', array('label' => 'Link Title', 'class' => 'span6'));
		echo $this->Form->input('description', array('class' => 'span6'));
		echo $this->Form->button('<i class="icon-white icon-plus-sign"></i> Submit',array('class'=>'btn btn-large btn-primary'));
	?>
	</fieldset>
<?php echo $this->Form->end(); ?>
</div>