<?php $this->set('suppressSubnav', true); ?>
<h2><?= $this->Html->image('ui/icons/system-monitor.png'); ?> Network Updates</h2>
<p>The latest action from all users.</p>

<?php if(empty($updates)) : ?>

<p class="alert alert-warning">No updates at this time, please check back soon.</p>

<?php else : ?>

<?= $this->element('admin/pagination'); ?>

<?= $this->element('common/updates-list'); ?>

<?= $this->element('admin/pagination', array('show_summary' => true)); ?>

<?php endif; ?>