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
<div class="row">
	<div class="col-md-2 text-right action-bar">&nbsp;
		<?php if(Access::isOwner($video['Video']['user_id']) || Access::hasRole('Admin')) : ?>
		<h3>Actions</h3>
		<ul class="list-unstyled actions">
			<li><?= $this->Html->link('<span class="glyphicon glyphicon-facetime-video"></span> Upload video files', array('controller' => 'videos', 'action' => 'upload', $video['Video']['id']), array('class' => 'btn btn-default btn-block btn-sm', 'escape' => false)); ?></li>
			<li><?= $this->Html->link('<span class="glyphicon glyphicon-pencil"></span> Change video text', array('controller' => 'videos', 'action' => 'edit', $video['Video']['id']), array('class' => 'btn btn-default btn-block btn-sm', 'escape' => false)); ?></li>
			<li><?= $this->Html->link('<span class="glyphicon glyphicon-picture"></span> Screenshot', array('controller' => 'videos', 'action' => 'image', $video['Video']['id']), array('class' => 'btn btn-default btn-block btn-sm', 'escape' => false)); ?></li>
		</ul>
		<?php endif; ?>
	</div>
	<div class="col-md-10">
		<div class="link-exchange link-view video-embed text-center">
			<h2><?= $video['Video']['title']; ?></h2>
			<p><?= $video['Video']['description']; ?><br><br></p>
			<?php if($video['Video']['mp4'] && file_exists(IMAGES . "{$video_path}.mp4")) :

				$video_tag = array(
					'src' => $this->Html->webroot(IMAGES_URL . "{$video_path}.mp4?t=" . filemtime(IMAGES . "{$video_path}.mp4")),
					'controls' => 'controls',
					'preload' => 'none',
					'type' => 'video/mp4',
					'id' => 'video-player'
				);

				App::import('Vendor', 'getID3/getid3/getid3');
				$getID3 = new getID3;
				$videoInfo = $getID3->analyze(IMAGES . "{$video_path}.mp4");

				$video_tag['width'] = $videoInfo['video']['resolution_x'];
				$video_tag['height'] = $videoInfo['video']['resolution_y'];

				// vertical video
				if($video_tag['height'] > $video_tag['width']) {
					if($video_tag['height'] > 700) {
						$video_tag['height'] = 700;
						$video_tag['width'] = ceil($video_tag['height'] * ($videoInfo['video']['resolution_x'] / $videoInfo['video']['resolution_y']));
					}
					$video_tag['class'] = 'vertical';
				// horizontal video
				} else {
					$video_tag['class'] = 'horizontal';
				}

				$this->Html->script('/lib/mediaelement-2.10.3/mediaelement-and-player.min.js', array('inline' => false));
				$this->Html->css('/lib/mediaelement-2.10.3/mediaelementplayer.min.css', null, array('inline' => false));

				echo $this->Html->tag('video', null, $video_tag);
			?>

			<?php $this->append('scriptBlock'); ?>
			<script>
				$('video').mediaelementplayer({
					videoWidth: <?= $video_tag['width']; ?>,
					videoHeight: <?= $video_tag['height']; ?>
				});
			</script>
			<?php $this->end(); ?>

			<?php else : ?>
			<p class="alert alert-info">No video attached at the moment, check back soon.</p>
			<?php endif; ?>

		</div>

		<p class="stats text-center"><br>
			<span class="glyphicon glyphicon-time"></span> <?php printf('%d min %02d sec', (int)($video['Video']['duration'] / 60), ($video['Video']['duration'] % 60)); ?>
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
		</p>
		<p class="text-center muted">Uploaded by <strong><?= $this->Html->link($video['User']['username'], array('controller' => 'videos', 'action' => 'index', 'user' => $video['User']['id'])); ?></strong> <?= $this->Time->timeAgoInWords($video['Video']['created']); ?>

		<h3 class="text-right"><?= $this->Html->image('ui/icons/balloon.png'); ?> Comments</h3>
		<?= $this->element('common/chat-module', array('model' => 'Video', 'foreign_key' => $video['Video']['id'])); ?>
	</div>
</div>

<?php $this->element('common/tag-tally', array('action' => (Access::isOwner($video['Video']['user_id'])) ? 'my_videos' : 'index')); ?>