<?php
$this->set('suppressSubnav', true);
$this->set('title', 'Updates');
?>
<h2><?= $this->Html->image('ui/icons/system-monitor.png'); ?> Network Updates</h2>
<p>
	The latest action from all users.

<?php
if(!isset($this->params['named']['view'])) {
	echo $this->Paginator->link('<i class="glyphicon glyphicon-search"></i> Include My Activity', array('view' => 'all', 'page' => 1), array('class' => 'small', 'escape' => false));
}
?>
</p>

<?php if(empty($updates)) : ?>

<p class="alert alert-warning">No updates at this time, please check back soon.</p>

<?php else : ?>

<?= $this->element('admin/pagination'); ?>

<?= $this->element('common/updates-list'); ?>

<?= $this->element('admin/pagination', array('show_summary' => true)); ?>

<?php endif; ?>