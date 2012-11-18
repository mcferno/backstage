<div class="row-fluid">
	<div class="links form span12">
	<?php echo $this->Form->create('Link'); ?>
		<h1>Edit Link</h1>
		<?php if($this->Session->read('Auth.User.id') !== $this->request->data['Link']['user_id']) : ?>
		<div class="alert alert-warning">
			<strong>Warning</strong> You are not the owner of this link. You are permitted to edit it, but focus on improving the quality of the information on the owner's behalf.
		</div>
		<?php endif; ?>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('user_id', array('type' => 'hidden'));
		echo $this->Form->input('url', array('label' => 'URL', 'class' => 'full', 'placeholder' => 'http://example.com'));
		echo $this->Form->input('title', array('label' => 'Link Title', 'class' => 'full', 'placeholder' => 'Website name, content title, or short descriptive name'));
		echo $this->Form->input('description', array('class' => 'full', 'rows' => 4, 'placeholder' => 'Describe the website or content to entice other users to check it out.'));
	?>
		<label>Tags (to group similar type links together)</label>
		<div class="clearfix">
			<?= $this->element('common/tagging', array('model' => 'Link', 'foreign_key' => $this->request->data['Link']['id'])); ?>
		</div>
		<p class="muted"><br><i class="icon-white icon-info-sign"></i> Try to re-use existing tags when possible. You may add new tags, but don't make them too specific, the idea is to have many links per tag.</p>

		<?= $this->Form->button('<i class="icon-white icon-plus-sign"></i> Submit',array('class'=>'btn btn-large btn-primary')); ?>
		<?= $this->Html->link('<i class="icon icon-ban-circle"></i> Cancel Edit', array('action' => 'view', $this->request->data['Link']['id']), array('class' => 'btn btn-large', 'escape' => false)); ?>
	<?php echo $this->Form->end(); ?>
	</div>
</div>