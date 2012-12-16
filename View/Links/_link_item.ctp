<?php
	$screenshot = "{$thumbnail_path}/{$link['Link']['id']}";
	if(file_exists(IMAGES_URL . "{$screenshot}.jpg")) {
		$screenshot .= '.jpg?' . filemtime(IMAGES_URL . "{$screenshot}.jpg");
	} elseif (file_exists(IMAGES_URL . "{$screenshot}.png")) {
		$screenshot .= '.png?' . filemtime(IMAGES_URL . "{$screenshot}.png");
	} else {
		$screenshot = false;
	}
?>
<div class="link-item <?php if(!$screenshot) { echo 'no-screenshot'; } ?> clearfix">

<?php if($screenshot) : ?>
<div class="screenshot">
	<a href="<?= $link['Link']['url']; ?>" target="_blank"><?= $this->Html->image($screenshot); ?></a>
</div>
<?php endif; // has screenshot ?>

<div class="title">
	<a href="<?= $link['Link']['url']; ?>" target="_blank" class="main"><?= $link['Link']['title']; ?></a> <span class="muted long-link"><span class="extra">« </span><a href="<?= $link['Link']['url']; ?>" target="_blank"><?= $link['Link']['url']; ?></a><span class="extra"> »</span></span>
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
	&mdash; posted <?= date('M j', strtotime($link['Link']['created'])); ?> by <strong><?= $this->Html->link($link['User']['username'], ($this->Session->read('Auth.User.id') == $link['Link']['user_id']) ? array('action' => 'my_links') : array('action' => 'index', 'user' => $link['Link']['user_id'])); ?></strong> <small>(<?= $this->Time->timeAgoInWords($link['Link']['created'], array('end' => '+1 year', 'accuracy' => array('month' => 'month'))); ?>)</small>
</div>

<div class="interact">
	<?php
		if(!isset($hideComments)) {
			$count = isset($message_tally[$link['Link']['id']]) ? $message_tally[$link['Link']['id']] : 0;
			$badge = ($count === 0) ? 'custom badge-off' : 'success';
			echo $this->Html->link('<span class="badge badge-' . $badge . ' comments">'. $count .' comments</span>', array('action' => 'view', $link['Link']['id']), array('escape' => false, 'class' => 'view-link'));
		}
	?>
	<?php foreach($link['Tag'] as $idx => $tag) : ?>
	<a href="<?= $this->Html->url(array('controller' => 'links', 'action' => 'index', 'tag' => $tag['id'])); ?>"><span class="badge badge-<?= ($idx % 2 == 0) ? 'info' : 'pale'; ?>"><?= $tag['name']; ?></span></a>
	<?php endforeach; ?>

	<?php if($this->Session->read('Auth.User.id') == $link['Link']['user_id'] || (int)$this->Session->read('Auth.User.role') >= ROLES_ADMIN) : ?>
	<div class="controls" style="display:none;">
		<?= $this->Html->link($this->Html->image('ui/icons/image-pencil.png') . ' Thumbnail', array('action' => 'image', $link['Link']['id']), array('class' => 'btn btn-mini btn-inverse', 'title' => 'Change this link\'s thumnail', 'escape' => false)); ?>
		<?= $this->Html->link($this->Html->image('ui/icons/pencil.png') . ' Edit', array('action' => 'edit', $link['Link']['id']), array('class' => 'btn btn-mini btn-inverse edit-btn', 'title' => 'Edit this link', 'escape' => false)); ?>
		<?= $this->Form->postLink($this->Html->image('ui/icons/prohibition.png') . ' Delete', array('action' => 'delete', $link['Link']['id']), array('class' => 'btn btn-mini btn-inverse', 'title' => 'Delete this link', 'escape' => false), "Are you sure you want to delete the {$link['Link']['title']} link?"); ?>
	</div>
	<?php endif; ?>
</div>

</div><!-- closing link item -->