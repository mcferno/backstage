<?php
	$video_path = "user/videos/{$video['Video']['id']}";
	$screenshot = (file_exists(IMAGES_URL . "{$video_path}.jpg")) ? "{$video_path}.jpg" : false;

	$video_sizes = array();
	if($video['Video']['mp4'] && file_exists(IMAGES_URL . "${video_path}.mp4")) {
		$video_sizes['mp4'] = filesize(IMAGES_URL . "${video_path}.mp4");
	}
	if($video['Video']['webm'] && file_exists(IMAGES_URL . "${video_path}.webm")) {
		$video_sizes['webm'] = filesize(IMAGES_URL . "${video_path}.webm");
	}
?>
<div class="link-item video-item <?php if(!$screenshot) { echo 'no-screenshot'; } ?> clearfix">

<?php if($screenshot) : ?>
<div class="screenshot">
	<?= $this->Html->image($screenshot, array('cachebust' => true, 'class' => 'pull-left', 'url' => array('controller' => 'videos', 'action' => 'view', $video['Video']['id']))); ?>
</div>
<?php endif; // has screenshot ?>

<div class="media-body">

<div class="title">
	<?= $this->Html->link($video['Video']['title'], array('controller' => 'videos', 'action' => 'view', $video['Video']['id']), array('class' => 'main')); ?>
</div>

<div class="description">
<?php
	if(empty($video['Video']['description'])) {
		echo $this->Html->tag('span', 'no description', array('class' => 'muted'));
	} else {
		echo $video['Video']['description'];
	}
?>
</div>

<div class="stats">
	&mdash; <span class="glyphicon glyphicon-time"></span> <?php printf('%d min %02d sec', (int)($video['Video']['duration'] / 60), ($video['Video']['duration'] % 60)); ?>
	<?php if($video['Video']['filmed'] != '0000-00-00') : ?>
	&middot; <span class="glyphicon glyphicon-facetime-video"></span> <?= date('M Y', strtotime($video['Video']['filmed'])); ?>
	<?php endif; ?>
	<?php if($video_sizes) : ?>
	&middot; <span class="glyphicon glyphicon-file"></span> <?= CakeNumber::toReadableSize(max($video_sizes)); ?>
	<!-- <?= json_encode($video_sizes); ?> -->
	<?php endif; ?>
	&middot; <span class="glyphicon glyphicon-film"></span> <?= ($video['Video']['hd'])? 'HD' : 'SD'; ?>
	<?php if(!empty($video['Video']['url'])) : $video_url = parse_url($video['Video']['url']); ?>
	&middot; <span class="glyphicon glyphicon-globe"></span> <?= str_replace('www.', '', $video_url['host']); ?>
	<?php endif; ?>
</div>

<div class="stats muted">
	&mdash; posted <?= date('M j', strtotime($video['Video']['created'])); ?> by <strong><?= $this->Html->link($video['User']['username'], (Access::isOwner($video['Video']['user_id'])) ? array('action' => 'my_videos') : array('action' => 'index', 'user' => $video['Video']['user_id'])); ?></strong> <small>(<?= $this->Time->timeAgoInWords($video['Video']['created'], array('end' => '+1 year', 'accuracy' => array('month' => 'month'))); ?>)</small>
</div>

<div class="interact">
	<?php
		if(!isset($hideComments)) {
			$count = isset($message_tally[$video['Video']['id']]) ? $message_tally[$video['Video']['id']] : 0;
			$badge = ($count === 0) ? 'custom badge-off' : 'success';
			echo $this->Html->link('<span class="badge badge-' . $badge . ' comments">'. $count .' comments</span>', array('action' => 'view', $video['Video']['id']), array('escape' => false, 'class' => 'view-video'));
		}
	?>
	<?php foreach($video['Tag'] as $idx => $tag) : ?>
	<a href="<?= $this->Html->url(array('controller' => 'videos', 'action' => 'index', 'tag' => $tag['id'])); ?>"><span class="badge badge-<?= ($idx % 2 == 0) ? 'info' : 'pale'; ?>"><?= $tag['name']; ?></span></a>
	<?php endforeach; ?>

	<?php if(!$this->request->is('mobile') && (Access::isOwner($video['Video']['user_id']) || Access::hasRole('Admin'))) : ?>
	<div class="controls" style="display:none;">
		<?= $this->Html->link($this->Html->image('ui/icons/image-pencil.png') . ' Thumbnail', array('action' => 'image', $video['Video']['id']), array('class' => 'btn btn-xs btn-inverse', 'title' => 'Change this video’s thumnail', 'escape' => false)); ?>
		<?= $this->Html->link($this->Html->image('ui/icons/pencil.png') . ' Edit', array('action' => 'edit', $video['Video']['id']), array('class' => 'btn btn-xs btn-inverse edit-btn', 'title' => 'Edit this video', 'escape' => false)); ?>
		<?= $this->Form->postLink($this->Html->image('ui/icons/prohibition.png') . ' Delete', array('action' => 'delete', $video['Video']['id']), array('class' => 'btn btn-xs btn-inverse', 'title' => 'Delete this video', 'escape' => false), "Are you sure you want to delete the {$video['Video']['title']} video?"); ?>
	</div>
	<?php endif; ?>
</div>

</div>

</div><!-- closing video item -->