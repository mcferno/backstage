<div class="tumblrs form">
<?php echo $this->Form->create('Tumblr');?>
	<fieldset>
		<legend><?php echo __('Add Tumblr'); ?></legend>
	<?php
		echo $this->Form->input('blog_name');
		echo $this->Form->input('tumblr_id');
		echo $this->Form->input('post_url');
		echo $this->Form->input('type');
		echo $this->Form->input('timestamp');
		echo $this->Form->input('text');
		echo $this->Form->input('source');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Tumblrs'), array('action' => 'index'));?></li>
	</ul>
</div>
