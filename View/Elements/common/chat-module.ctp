<?php
$this->Html->script(array('https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.3.1/jquery.cookie.min.js'),false);

// settings to pass to javascript module
$chatSettings = array(
	// username reference to highlight mentions
	'self' => $this->Session->read('Auth.User.username')
);
$chatSettings['scope'] = (!empty($model)) ? $model : 'Chat';
$chatSettings['scopeId'] = (!empty($foreign_key)) ? $foreign_key : null;
$chatSettings['mobile'] = $this->request->is('mobile');

// chat settings
if($chatSettings['scope'] === 'Chat') {
	$reverseChat = true;
	$submitLabel = 'Post';
	$submitClass = 'primary';

	$chatSettings['tones'] = array(
		'notify' => array(
			'mp3' => array(
				'format' => 'audio/mpeg',
				'file' => $this->Html->webroot('/audio/standard.mp3')
			),
			'ogg' => array(
				'format' => 'audio/ogg',
				'file' => $this->Html->webroot('/audio/standard.ogg')
			)
		),
		'alert' => array(
			'mp3' => array(
				'format' => 'audio/mpeg',
				'file' => $this->Html->webroot('/audio/attention.mp3')
			),
			'ogg' => array(
				'format' => 'audio/ogg',
				'file' => $this->Html->webroot('/audio/attention.ogg')
			)
		)
	);

	$chatSettings['url'] = $this->Html->url(array('controller' => 'users', 'action' => 'group_chat'));

// non-chat integration
} else {
	$reverseChat = false;
	$submitLabel = 'Send';
	$submitClass = 'info';
	$chatSettings['order'] = 'asc';
}

?>
<a name="comments"></a><a name="chat"></a>
<?php $this->start('chat-bar'); ?>
<div class="row">
<nav class="navbar chat-bar">
	<div class="msg">
		<form class="form-inline" role="form">
			<div class="text-input">
				<textarea type="text" class="form-control" placeholder="Type something…" rows="1"></textarea>
			</div>
			<button type="submit" class="btn btn-<?= $submitClass; ?>"><strong><?= $submitLabel; ?></strong></button>
		</form>
	</div>
</nav>
</div>
<?php $this->end(); ?>

<?php if($reverseChat) { echo $this->fetch('chat-bar'); } ?>

<div class="row">
	<div class="col-md-12">
		<div class="loading">Loading ...</div>
	</div>
</div>
<div class="row chat-window">
	<div class="col-md-12 chat <?= ($reverseChat) ? 'striped-inverse' : 'striped'; ?>"></div>
</div>

<?php if(!$reverseChat) { echo $this->fetch('chat-bar'); } ?>

<script type="text/template" id="chatRowTemplate">
<div class="chat-row">
	<div class="col-sm-2 col-xs-12">
		<div class="row">
			<div class="handle col-xs-6 col-sm-12"><%= username %></div>
			<div class="time col-xs-6 col-sm-12"><%= date %></div>
		</div>
	</div>
	<div class="message col-xs-12 col-sm-10"><%= message %></div>
</div>
</script>
<script type="text/template" id="embeddedImageTemplate">
<a href="<%= url %>" target="_blank" class="posted-content"><img src="<%= url %>" title="Image link posted by @<%= username %>"></a><a href="<%= url %>" class="original-link" target="_blank" style="display:none;"><%= url %></a><a class="close" href="#" title="Hide this image">×</a>
</script>
<script type="text/template" id="embeddedYouTubeTemplate">
<iframe width="640" height="360" src="https://www.youtube.com/embed/<%= video_id %>" frameborder="0" allowfullscreen class="posted-content"></iframe><a href="<%= url %>" target="_blank" class="original-link"><%= url %></a><a class="close" href="#" title="Hide this video">×</a><br>
</script>
<script type="text/template" id="embeddedVimeoTemplate">
<iframe src="https://player.vimeo.com/video/<%= video_id %>" width="640" height="360" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen class="posted-content"></iframe><a href="<%= url %>" target="_blank" class="original-link"><%= url %></a><a class="close" href="#" title="Hide this video">×</a><br>
</script>
<script type="text/javascript">
AppEnv['Config']['GroupChat'] = <?= json_encode($chatSettings); ?>;
</script>