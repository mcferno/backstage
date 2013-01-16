<?php
	$this->set('contentSpan', 10);

	$video_path = IMAGES_URL . "user/videos/{$video['Video']['id']}";
?>
<div class="row-fluid">
	<div class="span2 text-right action-bar">
		<h3>Actions</h3>
		<ul class="unstyled actions">
			<li><?= $this->Html->link('<i class="icon icon-pencil"></i> Change video text', array('controller' => 'videos', 'action' => 'edit', $video['Video']['id']), array('class' => 'btn btn-small', 'escape' => false)); ?></li>
			<li><?= $this->Html->link('<i class="icon icon-picture"></i> Screenshot', array('controller' => 'videos', 'action' => 'image', $video['Video']['id']), array('class' => 'btn btn-small', 'escape' => false)); ?></li>
		</ul>
	</div>
	<div class="span10">
		<div class="link-exchange link-view text-center">
			<h2><?= $video['Video']['title']; ?></h2>
			<p><?= $video['Video']['description']; ?></p>
			<video controls>
				<?php if($video['Video']['mp4'] && file_exists("{$video_path}.mp4")) : ?>
				<source type="video/mp4" src="<?= $this->Html->webroot("{$video_path}.mp4?t=" . filemtime("{$video_path}.mp4")); ?>" />
				<?php endif; ?>
				<?php if($video['Video']['webm'] && file_exists("{$video_path}.webm")) : ?>
				<source type="video/webm" src="<?= $this->Html->webroot("{$video_path}.webm?t=" . filemtime("{$video_path}.webm")); ?>" />
				<?php endif; ?>
				<?php /*
				<object width="640" height="360" type="application/x-shockwave-flash" data="__FLASH__.SWF">
					<param name="movie" value="__FLASH__.SWF" />
					<param name="flashvars" value="autostart=true&amp;controlbar=over&amp;image=__POSTER__.JPG&amp;file=__VIDEO__.MP4" />
					<img src="__VIDEO__.JPG" width="640" height="360" alt="__TITLE__"
						title="No video playback capabilities, please download the video below" />
				</object>
				*/ ?>
			</video>
		</div>

		<h3 class="text-right"><?= $this->Html->image('ui/icons/balloon.png'); ?> Comments</h3>
		<?= $this->element('common/chat-module', array('model' => 'Video', 'foreign_key' => $video['Video']['id'])); ?>
	</div>
</div>

<?php $this->element('common/tag-tally', array('action' => ($video['Video']['user_id'] == $this->Session->read('Auth.User.id') ? 'my_videos' : 'index'))); ?>