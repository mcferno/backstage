<?php 
	$this->set('suppressSubnav', true);
	$this->set('contentSpan',12);
?>
<div class="span3 login">
	<h1>Login</h1>
	<?= $this->Session->flash(); ?>
	<?= $this->Session->flash('auth'); ?>
	<div class="users form">
	<?php echo $this->Form->create('User');?>
		<fieldset>
			<legend></legend>
		<?php
			echo $this->Form->input('username', array('autofocus' => 'autofocus'));
			echo $this->Form->input('password');
		?>
		</fieldset>
	<?php echo $this->Form->end(array('label' => 'Log In', 'class'=>'btn btn-primary'));?>
	</div>
</div>