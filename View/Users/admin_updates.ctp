<?php
$this->set('suppressSubnav', true);
$this->set('title', 'Updates');
$this->set('contentSpan', 6);
?>
<h2><?= $this->Html->image('ui/icons/system-monitor.png'); ?> Network Updates</h2>
<p class="pull-right">
<?php
if(!isset($this->params['named']['view'])) {
	echo $this->Paginator->link('<i class="glyphicon glyphicon-search"></i> Include My Updates', array('view' => 'all', 'page' => 1), array('class' => 'small', 'escape' => false));
}
?>
</p>

<?php if(empty($updates)) : ?>

<p class="alert alert-warning">No updates at this time, please check back soon.</p>

<?php else : ?>

<?= $this->element('common/updates-list-expanded'); ?>

<?= $this->element('admin/pagination', array('show_summary' => true)); ?>

<?php endif; ?>