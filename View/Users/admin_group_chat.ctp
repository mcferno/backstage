<?php $this->set('suppressSubnav', true); ?>
<h2>Group Chat.</h2>
<?= $this->element('common/chat-module'); ?>

<?php $this->start('sidebar-top'); ?>

<h3 class="active-label">Active <span class="badge badge-info active-count"><?= count($onlineUsers); ?></span></h3>
<ul class="online-users list-unstyled"></ul>

<h4 class="idle-label">Away from Chat <span class="badge idle-count">0</span></h4>
<ul class="idle-users list-unstyled"></ul>

<div class="tips">
	<p><span class="glyphicon glyphicon-info-sign"></span> Use <strong>@username</strong> to get that user's attention with your message.</p>
	<p><span class="glyphicon glyphicon-info-sign"></span> Use <strong>@all</strong> to highlight a message to all users.</p>
</div>

<?php if(!$this->request->is('mobile')) : ?>

<h4>Sound Settings</h4>
<ul class="list-unstyled">
<li class="mention-setting">
	<button class="btn btn-xs btn-inverse state-on" title="Turn OFF &#64;mention notification"><span class="glyphicon glyphicon-volume-up"></span></button><button class="btn btn-danger btn-xs state-off" title="Turn ON &#64;mention notification" style="display: none;"><span class="glyphicon glyphicon-volume-off"></span></button> direct messages
</li>
<li class="notification-setting">
	<button class="btn btn-xs btn-inverse state-on" title="Turn OFF new message notification"><span class="glyphicon glyphicon-volume-up"></span></button><button class="btn btn-danger btn-xs state-off" title="Turn ON new message notification" style="display: none;"><span class="glyphicon glyphicon-volume-off"></span></button> new messages
</li>
</ul>

<?php endif; // desktop ?>

<?php $this->end(); ?>