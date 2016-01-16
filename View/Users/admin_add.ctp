<div class="users form">
<?php
echo $this->Form->create('User', array(
	'inputDefaults' => array(
		'class' => 'form-control'
	)
));
$this->set('title', 'Add a New User');
?>
	<fieldset>
		<legend><?php echo __('Add a New User'); ?></legend>

		<div class="row">
			<div class="col-md-6">
			<?php
				echo $this->Form->input('username');
				echo $this->Form->input('email');
			?>
			</div>
			<div class="col-md-6">
			<?php
				echo $this->Form->input('password');
				echo $this->Form->input('role', array('options' => Access::$assignableRoles));
			?>
			</div>
		</div>
	</fieldset>
	<?= $this->Form->button('Submit', array('class' => 'btn btn-primary')); ?>
	<?= $this->Form->end();?>
</div>