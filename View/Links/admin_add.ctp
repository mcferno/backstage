<div class="links form">
<?php echo $this->Form->create('Link'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('user_id');
		echo $this->Form->input('url');
		echo $this->Form->input('title');
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
