<h1>Password reset for <?= $site_name; ?></h1>
<p>You have requested to reset you password on <?= $_SERVER['SERVER_NAME']; ?> on <?= date('M jS, Y'); ?> at <?= date('g:iA'); ?></p>
<p><strong>Click <a href="<?= $reset_url; ?>">here</a> to reset your password.</strong></p>
<p><small>This link will only work for a few hours. You can generate a new one <?= $this->Html->link('here', array('controller' => 'users', 'action' => 'forgot', 'full_base' => true)); ?> if you need.</small></p>