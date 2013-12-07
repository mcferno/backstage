<?php if($this->Session->check('Auth.User')) : ?>

<div class="album-module modal" style="display:none;" role="dialog">
<div class="modal-dialog">
<div class="modal-content">

	<div class="modal-header">
		<a class="close" data-dismiss="modal">×</a>
		<h3><?= $this->Html->image('ui/icons/image-import.png'); ?> <?= (isset($this->request->data['Album']['id'])) ? 'Edit' : 'Create a new'; ?> Album</h3>
	</div>
	<?php
		echo $this->Form->create('Album', array('url' => array('action' => 'save'), 'type' => 'file'));
		echo $this->Form->input('id', array('type' => 'hidden'));
		echo $this->Form->input('shared', array('type' => 'hidden', 'value' => 1));
	?>
	<div class="modal-body">
		<div class="assets form">
			<h4>Album details</h4>
			<?= $this->Form->input('title', array('type' => 'text', 'label' => false, 'class' => 'form-control', 'placeholder' => 'Album title')); ?>
			<?= $this->Form->input('description', array('type' => 'text', 'label' => false, 'class' => 'form-control', 'placeholder' => 'Album description (optional)', 'rows' => 4)); ?>
			<?= $this->Form->input('location', array('type' => 'text', 'label' => false, 'class' => 'form-control', 'placeholder' => 'Where and when were these photos taken? (optional)')); ?>
		</div>
		<div class="progress progress-striped active" style="display:none;">
			<div class="progress-bar progress-bar-info bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
		</div>
	</div>
	<div class="modal-footer">
		<?= $this->Form->button('<span class="glyphicon glyphicon-plus-sign"></span> ' . (isset($this->request->data['Album']['id']) ? 'Update' : 'Create'), array('class'=>'btn btn-success btn-upload', 'data-loading-text' => "<span class='glyphicon glyphicon-upload'></span> Saving ...")); ?>
	</div>
	<?php echo $this->Form->end(); ?>

</div>
</div>	
</div>

<?php endif; // Authenticated users ?>