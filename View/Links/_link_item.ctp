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
	<?php
		$count = isset($message_tally[$link['Link']['id']]) ? $message_tally[$link['Link']['id']] : 0;
		$badge = ($count === 0) ? 'custom badge-off' : 'success';
		echo $this->Html->link('<span class="badge badge-' . $badge . ' comments">'. $count .' comments</span>', array('action' => 'view', $link['Link']['id']), array('escape' => false)); ?>
	<?php foreach($link['Tag'] as $idx => $tag) : ?>
	<a href="<?= $this->Html->url(array('controller' => 'links', 'action' => 'index', 'tag' => $tag['id'])); ?>"><span class="badge badge-<?= ($idx % 2 == 0) ? 'info' : 'pale'; ?>"><?= $tag['name']; ?></span></a>
	<?php endforeach; ?>

	<?php if($this->Session->read('Auth.User.id') == $link['Link']['user_id'] || (int)$this->Session->read('Auth.User.role') >= ROLE_ADMIN) : ?>
	<div class="controls" style="display:none;">
		<?= $this->Html->link($this->Html->image('ui/icons/pencil.png') . ' Edit', array('action' => 'edit', $link['Link']['id']), array('class' => 'btn btn-inverse edit-btn', 'title' => 'Edit this link', 'escape' => false)); ?>
		<?= $this->Form->postLink($this->Html->image('ui/icons/prohibition.png') . ' Delete', array('action' => 'delete', $link['Link']['id']), array('class' => 'btn btn-inverse', 'title' => 'Delete this link', 'escape' => false), "Are you sure you want to delete the {$link['Link']['title']} link?"); ?>
	</div>
	<?php endif; ?>
</div>