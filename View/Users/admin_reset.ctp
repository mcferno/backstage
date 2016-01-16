<?php $this->set('suppressSubnav', true); ?>
<div class="users form">
	<?php
	echo $this->Form->create('User');
	echo $this->Form->input('id');
	$this->set('title', 'Password Reset');
	?>
	<fieldset>
		<legend>Password Reset</legend>

		<p>Set a new password below.</p>

		<div class="row">
			<div class="col-md-6">
				<?= $this->Form->input('username', array('class' => 'form-control', 'disabled' => 'disabled')); ?>

			</div>
			<div class="col-md-6">
				<?= $this->Form->input('password', array('value' => '', 'label' => 'New Password', 'class' => 'form-control', 'placeholder' => 'Set a new password')); ?>
			</div>
		</div>
	</fieldset>
	<?= $this->Form->button('<i class="glyphicon glyphicon-ok-circle"></i> Save Changes', array('class' => 'btn btn-primary pull-right', 'escape' => false)); ?>
	<?= $this->Form->end(); ?>

</div>
<div class="clearfix"></div>