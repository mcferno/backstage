<?php 
	if(!Access::hasRole('Admin')) { 
		$this->set('suppressSubnav',true); 
	}
?>
<div class="users form">
<?php echo $this->Form->create('User');?>
	<fieldset>
		<legend>
			<?= ($this->Form->value('id') == $this->Session->read('Auth.User.id'))?'Modify your account':'Modify user account'; ?>
		</legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('username');
		echo $this->Form->input('password',array('value'=>'','label'=>'New Password'));
	?>	
		<div class="alert alert-info">Leave blank to keep current password.</div>
	<?php
		if(Access::hasRole('Admin')) {
			echo $this->Form->input('role', array('options' => Access::$assignableRoles));
		}
		echo $this->Form->button('Submit',array('class'=>'btn btn-primary'));
	?>
	</fieldset>
<?php echo $this->Form->end();?>
</div>