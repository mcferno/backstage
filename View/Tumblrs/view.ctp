<div class="tumblrs view">
<h2><?php  echo __('Tumblr');?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($tumblr['Tumblr']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Blog Name'); ?></dt>
		<dd>
			<?php echo h($tumblr['Tumblr']['blog_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Tumblr Id'); ?></dt>
		<dd>
			<?php echo h($tumblr['Tumblr']['tumblr_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Post Url'); ?></dt>
		<dd>
			<?php echo h($tumblr['Tumblr']['post_url']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo h($tumblr['Tumblr']['type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Timestamp'); ?></dt>
		<dd>
			<?php echo h($tumblr['Tumblr']['timestamp']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Text'); ?></dt>
		<dd>
			<?php echo h($tumblr['Tumblr']['text']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Source'); ?></dt>
		<dd>
			<?php echo h($tumblr['Tumblr']['source']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Tumblr'), array('action' => 'edit', $tumblr['Tumblr']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Tumblr'), array('action' => 'delete', $tumblr['Tumblr']['id']), null, __('Are you sure you want to delete # %s?', $tumblr['Tumblr']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Tumblrs'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Tumblr'), array('action' => 'add')); ?> </li>
	</ul>
</div>
