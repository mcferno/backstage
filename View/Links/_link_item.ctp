<?php
	$screenshot = (isset($link['Link']['thumbnail'])) ? $link['Link']['thumbnail'] : false;
	$url_parts = parse_url($link['Link']['url']);
	$target = (stripos($url_parts['host'], $_SERVER['HTTP_HOST']) === false) ? '_blank' : '_top';
?>
<div class="link-item <?php if(!$screenshot) { echo 'no-screenshot'; } ?> clearfix">

<?php if($screenshot) : ?>
<div class="screenshot">
	<a href="<?= $link['Link']['url']; ?>" target="<?= $target; ?>" class="pull-left"><?= $this->Html->image($screenshot, array('cachebust' => true, 'class' => "media-object")); ?></a>
</div>
<?php endif; // has screenshot ?>

<div class="media-body">

<div class="title">
	<a href="<?= $link['Link']['url']; ?>" target="<?= $target; ?>" class="main"><?= $link['Link']['title']; ?></a> <span class="muted long-link"><span class="extra">« </span><a href="<?= $link['Link']['url']; ?>" target="<?= $target; ?>"><?= $link['Link']['url']; ?></a><span class="extra"> »</span></span>
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
	&mdash; posted <?= date('M j', strtotime($link['Link']['created'])); ?> by
	<strong><?= $this->Html->link($link['User']['username'], (Access::isOwner($link['Link']['user_id'])) ? array('action' => 'my_links') : array('action' => 'index', 'user' => $link['Link']['user_id'])); ?></strong>
	<small>
		(<?= $this->Time->timeAgoInWords($link['Link']['created'], array('end' => '+1 year', 'accuracy' => array('month' => 'month'))); ?>)
		<?php if($link['Link']['sticky']) : ?><span class="glyphicon glyphicon-pushpin" title="Pinned"></span><?php endif; ?>
	</small>
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

	<?php if(!$this->request->is('mobile') && (Access::isOwner($link['Link']['user_id']) || Access::hasRole('Admin'))) : ?>
	<div class="controls" style="display:none;">
		<?= $this->Html->link($this->Html->image('ui/icons/image-pencil.png') . ' Thumbnail', array('action' => 'image', $link['Link']['id']), array('class' => 'btn btn-xs btn-inverse', 'title' => 'Change this link’s thumnail', 'escape' => false)); ?>
		<?= $this->Html->link($this->Html->image('ui/icons/pencil.png') . ' Edit', array('action' => 'edit', $link['Link']['id']), array('class' => 'btn btn-xs btn-inverse edit-btn', 'title' => 'Edit this link', 'escape' => false)); ?>
		<?= $this->Form->postLink($this->Html->image('ui/icons/prohibition.png') . ' Delete', array('action' => 'delete', $link['Link']['id']), array('class' => 'btn btn-xs btn-inverse', 'title' => 'Delete this link', 'escape' => false), "Are you sure you want to delete the {$link['Link']['title']} link?"); ?>
	</div>
	<?php endif; ?>
</div>

</div>

</div><!-- closing link item -->