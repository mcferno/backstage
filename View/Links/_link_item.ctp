<div class="title">
	<a href="<?= $link['Link']['url']; ?>" target="_blank" class="main"><?= $link['Link']['title']; ?></a> <span class="muted">« <a href="<?= $link['Link']['url']; ?>" target="_blank"><?= $link['Link']['url']; ?></a> »</span>
</div>
<div class="description">
<?php
	if(empty($link['Link']['description'])) {
		echo $this->Html->tag('span', 'no description', array('class' => 'muted')); 
	} else {
		echo $link['Link']['description'];
	}
?>
</div>
<div class="stats muted">
	&mdash; posted <?= date('M j', strtotime($link['Link']['created'])); ?> by <strong><?= $link['User']['username']; ?></strong> <small>(<?= $this->Time->timeAgoInWords($link['Link']['created'], array('end' => '+1 year', 'accuracy' => array('month' => 'month'))); ?>)</small>
</div>
<div class="interact">
	<?= $this->Html->link('<span class="badge badge-success comments">3 comments</span>', array('action' => 'view', $link['Link']['id']), array('escape' => false)); ?>
	<span class="badge badge-info">Game</span>
	<span class="badge badge-pale">HTML5</span>
	<span class="badge badge-info">Chrome</span>
	<span class="badge badge-pale">Audio</span>

	<?php if($this->Session->read('Auth.User.id') == $link['Link']['user_id'] || (int)$this->Session->read('Auth.User.role') >= ROLE_ADMIN) : ?>
	<div class="controls" style="display:none;">
		<?= $this->Html->link($this->Html->image('ui/icons/pencil.png') . ' Edit', array('action' => 'edit', $link['Link']['id']), array('class' => 'btn btn-inverse edit-btn', 'title' => 'Edit this link', 'escape' => false)); ?>
		<?= $this->Form->postLink($this->Html->image('ui/icons/prohibition.png') . ' Delete', array('action' => 'delete', $link['Link']['id']), array('class' => 'btn btn-inverse', 'title' => 'Delete this link', 'escape' => false), "Are you sure you want to delete the {$link['Link']['title']} link?"); ?>
	</div>
	<?php endif; ?>
</div>