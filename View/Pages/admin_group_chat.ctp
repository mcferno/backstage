<?php 
	$this->set('suppressSubnav', true);
	$this->Html->script(array('utilities'),false);
?>
<div class="row">
	<div class="span12"><h1>Group Chat.</h1></div>
</div>
<div class="row">
	<div class="span12">
		<div class="loading">Loading ...</div>
	</div>
</div>
<div class="row chat-window">
	<div class="span9">
		<table class="table table-striped chat table-condensed"></table>
	</div>
</div>

<div class="navbar navbar-fixed-bottom chat-bar">
	<div class="navbar-inner">
		<div class="container">			
			<!-- Be sure to leave the brand out there if you want it shown -->
			<h3 class="handle"><?= $this->Session->read('Auth.User.username'); ?></h3>
			
			<form class="navbar-form pull-left msg">
				<input type="text" class="span6" placeholder="Type something…">
				<button type="submit" class="btn btn-primary"><strong>Send</strong></button>
			</form>
			
			<?= $this->element('admin/loading-animation'); ?>
			
			<!-- Everything you want hidden at 940px or less, place within here -->
			<div class="nav-collapse">
			<!-- .nav, .navbar-search, .navbar-form, etc -->
			</div>
		
		</div>
	</div>
</div>

<script type="text/template" id="chatRowTemplate">
<td class="time"><%= date %></td>
<td class="handle"><%= username %></td>
<td class="message"><%= message %></td>
</script>