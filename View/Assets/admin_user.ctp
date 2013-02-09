<h1><?= $user['User']['username']; ?>'s images</h1>
<p class="tall"><?= $user['User']['username']; ?> has a total of <span class="badge <?= (count($images))?'badge-custom':''; ?>"><?= $this->Paginator->counter(array('format' =>'{:count}')); ?></span> images</p>


<?= $this->element('admin/pagination'); ?>

<div class="image-wall">
<?php 
	foreach ($images as $image) {
		echo $this->Html->link($this->Html->image($user_dir . '200/' . $image['Asset']['filename']),array('action'=>'view',$image['Asset']['id']),array('escape'=>false));
	} 
?>
</div>

<?= $this->element('admin/pagination', array('show_summary' => true)); ?>