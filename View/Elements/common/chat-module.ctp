<?php
$this->Html->script(array('utilities'),false);

$submit_label = isset($submitLabel) ? $submitLabel : 'Send';
$reverseChat = true;

$chatSettings = array();
if(isset($msgOrder) && in_array($msgOrder, array('asc', 'desc'))) {
	$chatSettings['order'] = $msgOrder;
	$reverseChat = ($msgOrder != 'asc');
}

$submitClass = isset($button) ? $button : 'primary';
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
					<button type="submit" class="btn btn-<?= $submitClass; ?>"><strong><?= $submit_label; ?></strong></button>
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
		<table class="table table-striped chat table-condensed"></table>
	</div>
</div>

<?php if(!$reverseChat) { echo $this->fetch('chat-bar'); } ?>

<script type="text/template" id="chatRowTemplate">
<td class="time"><%= date %></td>
<td class="handle"><%= username %></td>
<td class="message"><%= message %></td>
</script>
<script type="text/javascript">
GroupChat.config = <?= json_encode($chatSettings); ?>;
</script>