<?php $this->set('suppressSubnav', true); ?>
<h2>Group Chat.</h2>
<?= $this->element('common/chat-module'); ?>

<?php $this->start('sidebar-top'); ?>

<h3 class="active-label">Active <span class="badge badge-info active-count"><?= count($onlineUsers); ?></span></h3>
<ul class="online-users unstyled"></ul>

<h4 class="idle-label">Away from Chat <span class="badge idle-count">0</span></h4>
<ul class="idle-users unstyled"></ul>

<div class="tips">
	<p><i class="icon-white icon-info-sign"></i> Use <strong>@username</strong> to get that user's attention with your message.</p>
	<p><i class="icon-white icon-info-sign"></i> Use <strong>@all</strong> to highlight a message to all users.</p>
</div>

<?php $this->end(); ?>