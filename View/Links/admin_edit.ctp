<div class="row-fluid">
<div class="links form">
<?php echo $this->Form->create('Link'); ?>
	<fieldset>
		<h1>Edit Link</h1>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('user_id', array('type' => 'hidden'));
		echo $this->Form->input('url', array('label' => 'URL', 'placeholder' => 'http://example.com', 'class' => 'span8'));
		echo $this->Form->input('title', array('label' => 'Link Title', 'class' => 'span8'));
		echo $this->Form->input('description', array('class' => 'span8'));
	?>
	<label>Tags</label>
	<?php
		echo $this->element('common/tagging', array('model' => 'Link', 'foreign_key' => $this->request->data['Link']['id']));
		echo $this->Form->button('<i class="icon-white icon-plus-sign"></i> Submit',array('class'=>'btn btn-large btn-primary')); 
	?>
	</fieldset>
<?php echo $this->Form->end(); ?>
</div>
</div>