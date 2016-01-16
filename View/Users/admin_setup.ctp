<div class="users form">
<?php
	echo $this->Form->create('User');
	echo $this->Form->input('role', array('type' => 'hidden', 'value' => 1));
?>
	<fieldset>
		<legend><?php echo __('Setup the Admin User'); ?></legend>
		<p>Create the primary admin account, which will manage guest user accounts.</p>
		<div class="row">
			<div class="col-md-6">
				<?= $this->Form->input('username', array('class' => 'form-control')); ?>
				<?= $this->Form->input('email', array('class' => 'form-control')); ?>
			</div>
			<div class="col-md-6">
				<?= $this->Form->input('password', array('class' => 'form-control'));?>
			</div>
		</div>
	</fieldset>
	<?= $this->Form->button('<i class="glyphicon glyphicon-plus"></i> Create User', array('class' => 'btn btn-primary pull-right', 'escape' => false)); ?>

<?php echo $this->Form->end(); ?>
</div>