<?php 
	$this->set('suppressSubnav', true);
	$this->Html->script(array('utilities'),false);
?>
<div class="row">
	<div class="span12"><h1>Group Chat.</h1></div>
</div>
<div class="row">
<div class="navbar chat-bar">
	<div class="navbar-inner">
		<div class="container">
			<div class="span8 msg">
				<form class="navbar-form pull-left">
					<input type="text" class="span7" placeholder="Type something…">
					<button type="submit" class="btn btn-primary"><strong>Send</strong></button>
				</form>
				
				<?= $this->element('admin/loading-animation'); ?>		
			</div>
			
			<div class="span4">
				<h3 class="handle"><?= $this->Session->read('Auth.User.username'); ?></h3>
				<h4><i class="icon-user icon"></i> <span class="extra">Users online</span>: <span class="badge online-count" data-title="<?= $this->Session->read('Auth.User.username'); ?>" rel="tooltip">1</span></h4>
			</div>
		</div>
	</div>
</div>

</div>

<div class="row">
	<div class="span12">
		<div class="loading">Loading ...</div>
	</div>
</div>
<div class="row chat-window">
	<div class="span12">
		<table class="table table-striped chat table-condensed"></table>
	</div>
</div>

<script type="text/template" id="chatRowTemplate">
<td class="time"><%= date %></td>
<td class="handle"><%= username %></td>
<td class="message"><%= message %></td>
</script>