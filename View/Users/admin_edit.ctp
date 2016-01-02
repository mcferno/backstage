<?php
	if(!Access::hasRole('Admin')) {
		$this->set('suppressSubnav', true);
	}
?>
<div class="users form">
<?php
	echo $this->Form->create('User');
	echo $this->Form->input('id');
	$this->set('title', (Access::isOwner($this->Form->value('id')) ? 'My Profile' : 'User Profile'));
?>
	<fieldset>
		<legend>
			<?= (Access::isOwner($this->Form->value('id'))) ? 'Modify your account' : 'Modify user account'; ?>
		</legend>

		<p>You may change your username or password below. Remember them for the next time you need to log in.</p>

		<div class="row">
			<div class="col-md-6">
				<?= $this->Form->input('username', array('class' => 'form-control')); ?>
				<?= $this->Form->input('email', array('class' => 'form-control')); ?>
				<?php
					if(Access::hasRole('Admin')) {
						echo $this->Form->input('role', array('options' => Access::$assignableRoles, 'class' => 'form-control'));
					}
				?>
			</div>
			<div class="col-md-6">
				<?= $this->Form->input('password', array('value' => '', 'label' => 'New Password', 'class' => 'form-control', 'required' => false, 'placeholder' => 'Leave blank to keep your current password')); ?>

				<?php if(!empty($groups)) : ?>
				<?= $this->Form->input('fb_target', array('options' => $groups, 'class' => 'form-control', 'label' => 'Facebook Group', 'empty' => '-- no group --')); ?>
				<?php endif; ?>
			</div>
		</div>
	</fieldset>
	<?= $this->Form->button('<i class="glyphicon glyphicon-ok-circle"></i> Save Changes', array('class' => 'btn btn-primary pull-right', 'escape' => false)); ?>
	<?= $this->Form->end(); ?>

	<p class="cozy">
		<?= $this->Html->link('<i class="glyphicon glyphicon-chevron-left"></i> Back to My Dashboard', $userHome, array('escape' => false)); ?>
	</p>

</div>
