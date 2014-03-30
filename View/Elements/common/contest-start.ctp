<div class="contest-start-popin modal" style="display:none;">
<div class="modal-dialog">
<div class="modal-content">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">×</a>
		<h3><?= $this->Html->image('ui/icons/image-import.png'); ?> Start Caption Battle</h3>
	</div>
	<?php echo $this->Form->create('Contest', array('url' => array('action' => 'admin_add'))); ?>
	<div class="modal-body">
		<div class="assets form">
			<fieldset>
				<?php
					echo $this->Form->input('asset_id', array('type' => 'hidden', 'value' => $asset['Asset']['id']));
					echo $this->Form->input('user_id', array('type' => 'hidden', 'value' => $this->Session->read('Auth.User.id')));
				?>
				<p class="pull-right image"><?= $this->Html->image($asset['Asset']['image-thumb']); ?></p>
				<p>This will start a caption contest with the current image. Other users will be able to submit their own captions using the Meme Generator, and you choose a winner at the end.</p>
				<p class="alert alert-warning"><strong>Do not use images with text already on it!</strong></p>

				<h4><?= $this->Html->image('ui/icons/sticky-note.png'); ?> Contest Description (optional)</h4>
				<?= $this->Form->input('message', array('type' => 'textarea', 'label' =>'', 'class' => 'asset-url form-control', 'placeholder' => 'Who can do the best caption with this?', 'spellcheck' => 'true')); ?>
				<?php $fb_target = $this->Session->read('Auth.User.fb_target'); if(!empty($fb_target)): ?>
				<p><span class="glyphicon glyphicon-info-sign"></span> This new battle will be annouced automatically on TYS.</p>
				<?php endif; ?>
			</fieldset>
		</div>
	</div>
	<div class="modal-footer">
		<?= $this->Form->button('<span class="glyphicon glyphicon-ok"></span> Start', array('class' => 'btn btn-success')); ?>
	</div>
	<?php echo $this->Form->end();?>
</div>
</div>
</div>
