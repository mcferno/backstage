<h1><?= $user['User']['username']; ?>'s images</h1>
<p class="tall"><?= $user['User']['username']; ?> has a total of <span class="badge <?= (count($images))?'badge-custom':''; ?>"><?= $image_total; ?></span> images</p>

<?php if(!empty($tag['Tag'])) : ?>
<h3 class="cozy">
	Viewing Images in the Category: <span class="badge badge-info active-tag"><?= $tag['Tag']['name']; ?></span> 
	<?= $this->Html->link('Clear &times;', array('action' => $this->request->action, $user['User']['id']), array('class' => 'badge badge-muted', 'escape' => false)); ?>
</h3>
<?php endif; //tag ?>

<?= $this->element('admin/pagination'); ?>

<div class="image-wall">
<?php 
	foreach ($images as $image) {
		echo $this->Html->link($this->Html->image($user_dir . '200/' . $image['Asset']['filename']),array('action'=>'view',$image['Asset']['id']),array('escape'=>false));
	} 
?>
</div>

<?= $this->element('admin/pagination', array('show_summary' => true)); ?>

<?php $this->element('common/tag-tally'); ?>