<?php if((int)$this->Session->read('Auth.User.role') < ROLES_ADMIN) { $this->set('suppressSubnav',true); } ?>
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
		if((int)$this->Session->read('Auth.User.role') >= ROLES_ADMIN) {
			echo $this->Form->input('role');
		}
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>