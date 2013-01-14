<?php
	$screenshot = (isset($video['Video']['thumbnail'])) ? $video['Video']['thumbnail'] : false;
	if(!class_exists('CakeNumber')) {
		App::uses('CakeNumber', 'Utility');
	}
	$video_path = IMAGES_URL . "user/videos/{$video['Video']['id']}";
?>
<div class="link-item <?php if(!$screenshot) { echo 'no-screenshot'; } ?> clearfix">

<?php if($screenshot) : ?>
<div class="screenshot">
	<a href="<?= $video['Video']['url']; ?>" target="_blank"><?= $this->Html->image($screenshot, array('cachebust' => true)); ?></a>
</div>
<?php endif; // has screenshot ?>

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
	&mdash; <i class="icon-white icon-time"></i> <?php printf('%d:%02d', (int)($video['Video']['duration'] / 60), ($video['Video']['duration'] % 60)); ?> min
	<?php if($video['Video']['mp4'] && file_exists("${video_path}.mp4")) : ?>
	&middot; <i class="icon-white icon-facetime-video"></i> <?= CakeNumber::toReadableSize(filesize("${video_path}.mp4")); ?>
	<?php endif; ?>
	<?php if(!empty($video['Video']['url'])) : $video_url = parse_url($video['Video']['url']); ?>
	&middot; <i class="icon-white icon-globe"></i> <?= str_replace('www.', '', $video_url['host']); ?>
	<?php endif; ?>
</div>

<div class="stats muted">
	&mdash; posted <?= date('M j', strtotime($video['Video']['created'])); ?> by <strong><?= $this->Html->link($video['User']['username'], ($this->Session->read('Auth.User.id') == $video['Video']['user_id']) ? array('action' => 'my_videos') : array('action' => 'index', 'user' => $video['Video']['user_id'])); ?></strong> <small>(<?= $this->Time->timeAgoInWords($video['Video']['created'], array('end' => '+1 year', 'accuracy' => array('month' => 'month'))); ?>)</small>
</div>

<div class="interact">
	<?php
		if(!isset($hideComments)) {
			$count = isset($message_tally[$video['Video']['id']]) ? $message_tally[$video['Video']['id']] : 0;
			$badge = ($count === 0) ? 'custom badge-off' : 'success';
			echo $this->Html->link('<span class="badge badge-' . $badge . ' comments">'. $count .' comments</span>', array('action' => 'view', $video['Video']['id']), array('escape' => false, 'class' => 'view-video'));
		}
	?>
	<?php /* foreach($video['Tag'] as $idx => $tag) : ?>
	<a href="<?= $this->Html->url(array('controller' => 'videos', 'action' => 'index', 'tag' => $tag['id'])); ?>"><span class="badge badge-<?= ($idx % 2 == 0) ? 'info' : 'pale'; ?>"><?= $tag['name']; ?></span></a>
	<?php endforeach; */ ?>

	<?php if(!$this->request->is('mobile') && ($this->Session->read('Auth.User.id') == $video['Video']['user_id'] || (int)$this->Session->read('Auth.User.role') >= ROLES_ADMIN)) : ?>
	<div class="controls" style="display:none;">
		<?= $this->Html->link($this->Html->image('ui/icons/image-pencil.png') . ' Thumbnail', array('action' => 'image', $video['Video']['id']), array('class' => 'btn btn-mini btn-inverse', 'title' => 'Change this video\'s thumnail', 'escape' => false)); ?>
		<?= $this->Html->link($this->Html->image('ui/icons/pencil.png') . ' Edit', array('action' => 'edit', $video['Video']['id']), array('class' => 'btn btn-mini btn-inverse edit-btn', 'title' => 'Edit this video', 'escape' => false)); ?>
		<?= $this->Form->postLink($this->Html->image('ui/icons/prohibition.png') . ' Delete', array('action' => 'delete', $video['Video']['id']), array('class' => 'btn btn-mini btn-inverse', 'title' => 'Delete this video', 'escape' => false), "Are you sure you want to delete the {$video['Video']['title']} video?"); ?>
	</div>
	<?php endif; ?>
</div>

</div><!-- closing video item -->