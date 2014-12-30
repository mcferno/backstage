<?php
	$this->set('suppressSubnav', true);
?>
<div class="col-sm-8 col-sm-offset-2 login">
	<h1>Login</h1>
	<?= $this->Session->flash(); ?>
	<?= $this->Session->flash('auth'); ?>
	<div class="users form">
	<?php echo $this->Form->create('User');?>
		<fieldset>
			<legend></legend>
		<?php
			echo $this->Form->input('username', array('autofocus' => 'autofocus', 'class' => 'form-control'));
			echo $this->Form->input('password', array('class' => 'form-control'));
		?>
		</fieldset>
	<?php echo $this->Form->end(array('label' => 'Log In', 'class' => 'btn btn-primary'));?>
	</div>
</div>