<?php $this->set('suppressSubnav', true); ?>
<h1>Login</h1>
<?= $this->Session->flash(); ?>
<?= $this->Session->flash('auth'); ?>
<div class="users form">
<?php echo $this->Form->create('User');?>
	<fieldset>
		<legend></legend>
	<?php
		echo $this->Form->input('username');
		echo $this->Form->input('password');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label' => 'Log In', 'class'=>'btn btn-primary'));?>
</div>