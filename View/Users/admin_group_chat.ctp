<?php 
	$this->set('suppressSubnav', true);
	$this->Html->script(array('utilities'),false);
?>
<div class="row-fluid">
	<div class="span12"><h1 class="pull-left">Group Chat.</h1></div>
</div>
<div class="row-fluid">
	<div class="span12 ">
		<div class="chat-stats">
			<h3 class="handle"><?= $this->Session->read('Auth.User.username'); ?></h3>
			<h4><i class="icon-user icon-white"></i> <span class="extra">Users online</span>: <span class="badge badge-info online-count" data-title="<?= $this->Session->read('Auth.User.username'); ?>" rel="tooltip"><?= count($onlineUsers); ?></span></h4>
		</div>
	</div>
</div>
<div class="row-fluid">
<div class="navbar chat-bar">
	<div class="navbar-inner">
		<div class="container">
			<div class="msg">
				<form class="navbar-form">
					<div class="text-input">
						<input type="text" placeholder="Type something…">
					</div>
					<button type="submit" class="btn btn-primary"><strong>Send</strong></button>
				</form>
			</div>
		</div>
	</div>
</div>

</div>

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

<script type="text/template" id="chatRowTemplate">
<td class="time"><%= date %></td>
<td class="handle"><%= username %></td>
<td class="message"><%= message %></td>
</script>