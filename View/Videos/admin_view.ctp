<?php
	$this->set('contentSpan', 10);

	$video_path = "user/videos/{$video['Video']['id']}";
	$video_sizes = array();
	if($video['Video']['mp4'] && file_exists(IMAGES . "${video_path}.mp4")) {
		$video_sizes['mp4'] = filesize(IMAGES . "${video_path}.mp4");
	}
	if($video['Video']['webm'] && file_exists(IMAGES . "${video_path}.webm")) {
		$video_sizes['webm'] = filesize(IMAGES . "${video_path}.webm");
	}
?>
<div class="row-fluid">
	<div class="span2 text-right action-bar">&nbsp;
		<?php if($video['Video']['user_id'] == $this->Session->read('Auth.User.id') || $this->Session->read('Auth.User.role') >= ROLES_ADMIN) : ?>
		<h3>Actions</h3>
		<ul class="unstyled actions">
			<li><?= $this->Html->link('<i class="icon icon-facetime-video"></i> Upload video files', array('controller' => 'videos', 'action' => 'upload', $video['Video']['id']), array('class' => 'btn btn-small', 'escape' => false)); ?></li>
			<li><?= $this->Html->link('<i class="icon icon-pencil"></i> Change video text', array('controller' => 'videos', 'action' => 'edit', $video['Video']['id']), array('class' => 'btn btn-small', 'escape' => false)); ?></li>
			<li><?= $this->Html->link('<i class="icon icon-picture"></i> Screenshot', array('controller' => 'videos', 'action' => 'image', $video['Video']['id']), array('class' => 'btn btn-small', 'escape' => false)); ?></li>
		</ul>
		<?php endif; ?>
	</div>
	<div class="span10">
		<div class="link-exchange link-view video-embed text-center">
			<h2><?= $video['Video']['title']; ?></h2>
			<p><?= $video['Video']['description']; ?><br><br></p>
			<?php if($video['Video']['mp4'] && file_exists(IMAGES . "{$video_path}.mp4")) : 
				$this->Html->script('/lib/mediaelement-2.10.3/mediaelement-and-player.min.js', array('inline' => false));
				$this->Html->css('/lib/mediaelement-2.10.3/mediaelementplayer.min.css', null, array('inline' => false));
			?>
			<video src="<?= $this->Html->webroot(IMAGES_URL . "{$video_path}.mp4?t=" . filemtime(IMAGES . "{$video_path}.mp4")); ?>" type="video/mp4" id="video-player" controls="controls" preload="none"></video>
			<script>$('video').mediaelementplayer();</script>
			<?php endif; // mp4 video player ?>

		</div>

		<p class="stats text-center"><br>
			<i class="icon-white icon-time"></i> <?php printf('%d min %02d sec', (int)($video['Video']['duration'] / 60), ($video['Video']['duration'] % 60)); ?>
			<?php if($video['Video']['filmed'] != '0000-00-00') : ?>
			&middot; <i class="icon-white icon-facetime-video"></i> <?= date('M Y', strtotime($video['Video']['filmed'])); ?>
			<?php endif; ?>
			<?php if($video_sizes) : ?>
			&middot; <i class="icon-white icon-file"></i> <?= CakeNumber::toReadableSize(max($video_sizes)); ?>
			<!-- <?= json_encode($video_sizes); ?> -->
			<?php endif; ?>
			&middot; <i class="icon-white icon-film"></i> <?= ($video['Video']['hd'])? 'HD' : 'SD'; ?>
			<?php if(!empty($video['Video']['url'])) : $video_url = parse_url($video['Video']['url']); ?>
			&middot; <i class="icon-white icon-globe"></i> <?= str_replace('www.', '', $video_url['host']); ?>
			<?php endif; ?>
		</p>
		<p class="text-center muted">Uploaded by <strong><?= $this->Html->link($video['User']['username'], array('controller' => 'videos', 'action' => 'index', 'user' => $video['User']['id'])); ?></strong> <?= $this->Time->timeAgoInWords($video['Video']['created']); ?>

		<h3 class="text-right"><?= $this->Html->image('ui/icons/balloon.png'); ?> Comments</h3>
		<?= $this->element('common/chat-module', array('model' => 'Video', 'foreign_key' => $video['Video']['id'])); ?>
	</div>
</div>

<?php $this->element('common/tag-tally', array('action' => ($video['Video']['user_id'] == $this->Session->read('Auth.User.id') ? 'my_videos' : 'index'))); ?>