<?php
$this->Html->script(array('utilities'),false);

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

// non-chat integration
} else {
	$reverseChat = false;
	$submitLabel = 'Send';
	$submitClass = 'info';
	$chatSettings['order'] = 'asc';
}

?>
<a name="comments"></a>
<?php $this->start('chat-bar'); ?>
<div class="row-fluid">
<div class="navbar chat-bar">
	<div class="navbar-inner">
		<div class="container">
			<div class="msg">
				<form class="navbar-form">
					<div class="text-input">
						<input type="text" placeholder="Type something…">
					</div>
					<button type="submit" class="btn btn-<?= $submitClass; ?>"><strong><?= $submitLabel; ?></strong></button>
				</form>
			</div>
		</div>
	</div>
</div>
</div>
<?php $this->end(); ?>

<?php if($reverseChat) { echo $this->fetch('chat-bar'); } ?>

<div class="row-fluid">
	<div class="span12">
		<div class="loading">Loading ...</div>
	</div>
</div>
<div class="row-fluid chat-window">
	<div class="span12">
		<table class="table chat table-condensed"></table>
	</div>
</div>

<?php if(!$reverseChat) { echo $this->fetch('chat-bar'); } ?>

<script type="text/template" id="chatRowTemplate">
<td class="time"><%= date %></td>
<td class="handle"><%= username %></td>
<td class="message"><%= message %></td>
</script>
<script type="text/template" id="embeddedImageTemplate">
<img src="<%= url %>" title="Image link posted by @<%= username %>" class="posted-image">
</script>
<script type="text/template" id="embeddedYouTubeTemplate">
<iframe width="640" height="360" src="http://www.youtube.com/embed/<%= video_id %>?feature=player_detailpage" frameborder="0" allowfullscreen></iframe>
</script>
<script type="text/template" id="embeddedVimeoTemplate">
<iframe src="http://player.vimeo.com/video/<%= video_id %>" width="640" height="360" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</script>
<script type="text/javascript">
GroupChat.config = <?= json_encode($chatSettings); ?>;
</script>