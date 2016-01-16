<?php
$this->set('suppressSubnav', true);
?>
<div class="col-sm-8 col-sm-offset-2 login">
	<h1>Forgot my password</h1>
	<p>Provide the email address matching your account and we'll send you a password reset.</p>
	<?= $this->Session->flash(); ?>
	<div class="users form">
		<?php echo $this->Form->create('User');?>
		<fieldset>
			<legend></legend>
			<?= $this->Form->input('email', array('class' => 'form-control')); ?>
		</fieldset>
		<?php echo $this->Form->end(array('label' => 'Send Password Reset', 'class' => 'btn btn-primary pull-left'));?>
		<?= $this->Html->link('Back to Login', array('action' => 'login'), array('class' => 'pull-right')); ?>
	</div>
</div>