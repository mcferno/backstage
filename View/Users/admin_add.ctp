<div class="users form">
<?php echo $this->Form->create('User');?>
	<fieldset>
		<legend><?php echo __('Admin Add User'); ?></legend>
	<?php
		echo $this->Form->input('username');
		echo $this->Form->input('password');
		echo $this->Form->input('role', array('options' => Access::$assignableRoles));
		echo $this->Form->button('Submit',array('class'=>'btn btn-primary'));
	?>
	</fieldset>
<?php echo $this->Form->end();?>
</div>