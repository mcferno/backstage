<div class="row">
	<div class="links form span12">
	<?php echo $this->Form->create('Link'); ?>
		<h1>Edit Link</h1>
		<?php if(!Access::isOwner($this->request->data['Link']['user_id'])) : ?>
		<div class="alert alert-warning">
			<strong>Warning</strong> You are not the owner of this link. You are permitted to edit it, but focus on improving the quality of the information on the owner's behalf.
		</div>
		<?php endif; ?>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('user_id', array('type' => 'hidden'));
		echo $this->Form->input('url', array('label' => 'URL', 'class' => 'form-control', 'placeholder' => 'http://example.com'));
		echo $this->Form->input('title', array('label' => 'Link Title', 'class' => 'form-control', 'placeholder' => 'Website name, content title, or short descriptive name'));
		echo $this->Form->input('description', array('class' => 'form-control', 'rows' => 4, 'placeholder' => 'Describe the website or content to entice other users to check it out.'));
		if(Access::hasRole('Admin')) {
			echo $this->Form->input('sticky');
		}
	?>
		<label>Tags (to group similar type links together)</label>
		<div class="clearfix">
			<?= $this->element('common/tagging', array('model' => 'Link', 'foreign_key' => $this->request->data['Link']['id'])); ?>
		</div>
		<p class="muted"><br><span class="glyphicon glyphicon-info-sign"></span> Try to re-use existing tags when possible. You may add new tags, but don't make them too specific, the idea is to have many links per tag.</p>

		<?= $this->Form->button('<span class="glyphicon glyphicon-plus-sign"></span> Submit',array('class'=>'btn btn-primary')); ?>
		<?= $this->Html->link('<span class="glyphicon glyphicon-ban-circle"></span> Cancel Edit', array('action' => 'view', $this->request->data['Link']['id']), array('class' => 'btn btn-default', 'escape' => false)); ?>
	<?php echo $this->Form->end(); ?>
	</div>
</div>