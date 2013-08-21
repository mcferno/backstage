<div class="row">
	<div class="links form span12">
	<?php echo $this->Form->create('Video'); ?>
		<h1>Edit Video</h1>
		<?php if(!Access::isOwner($this->request->data['Video']['user_id'])) : ?>
		<div class="alert alert-warning">
			<strong>Warning</strong> You are not the owner of this link. You are permitted to edit it, but focus on improving the quality of the information on the owner's behalf.
		</div>
		<?php endif; ?>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('user_id', array('type' => 'hidden'));
		echo $this->Form->input('title', array('label' => 'Video Title', 'class' => 'full', 'placeholder' => 'Video name, or short descriptive title'));
	?>
		<div class="row">
			<div class="col-md-4"><?= $this->Form->input('duration', array('label' => 'Duration (MM:SS)', 'type' => 'text', 'placeholder' => '05:14')); ?></div>
			<div class="col-md-8"><?= $this->Form->input('filmed', array('label' => 'Date Filmed (approximate when unknown)', 'type' => 'date', 'dateFormat' => 'MY', 'maxYear' => date('Y') + 1,  'minYear' => date('Y') - 20)); ?></div>
		</div>
		<?= $this->Form->input('description', array('class' => 'full', 'rows' => 4, 'placeholder' => 'Describe the video content and why someone should be compelled to watch it.')); ?>
		<?= $this->Form->input('mp4'); ?><?= $this->Form->input('webm'); ?><?= $this->Form->input('hd'); ?>
		<label>Tags (to group similar type links together)</label>
		<div class="clearfix">
			<?= $this->element('common/tagging', array('model' => 'Video', 'foreign_key' => $this->request->data['Video']['id'])); ?>
		</div>
		<p class="muted"><br><span class="glyphicon glyphicon-info-sign"></span> Try to re-use existing tags when possible. You may add new tags, but don't make them too specific, the idea is to have many links per tag.</p>

		<?= $this->Form->button('<span class="glyphicon glyphicon-plus-sign"></span> Submit',array('class'=>'btn btn-large btn-primary')); ?><br><br>
		<?= $this->Html->link('<span class="glyphicon glyphicon-facetime-video"></span> Upload Video Files', array('action' => 'upload', $this->request->data['Video']['id']), array('class' => 'btn', 'escape' => false)); ?>
		<?= $this->Html->link('<span class="glyphicon glyphicon-search"></span> View Video', array('action' => 'view', $this->request->data['Video']['id']), array('class' => 'btn', 'escape' => false)); ?>
	<?php echo $this->Form->end(); ?>
	</div>
</div>