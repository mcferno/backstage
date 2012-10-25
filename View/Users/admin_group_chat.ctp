<div class="row-fluid">
	<div class="span12"><h1 class="pull-left">Group Chat.</h1></div>
</div>
<div class="row-fluid">
	<div class="span12">
		<div class="chat-stats">
			<h3 class="handle"><?= $this->Session->read('Auth.User.username'); ?></h3>
			<h4><i class="icon-user icon-white"></i> <span class="extra">Users online</span>: <span class="badge badge-info online-count" data-title="<?= $this->Session->read('Auth.User.username'); ?>" rel="tooltip"><?= count($onlineUsers); ?></span></h4>
		</div>
	</div>
</div>
<?php 
	$this->set('suppressSubnav', true); 
	echo $this->element('common/chat-module');
?>