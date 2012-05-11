<h1><?= $user['User']['username']; ?>'s images</h1>
<p><?= $user['User']['username']; ?> has a total of <span class="badge <?= (count($images))?'badge-custom':''; ?>"><?= $this->Paginator->counter(array('format' =>'{:count}')); ?></span> images</p>


<?= $this->element('admin/pagination',array('show_summary'=>true)); ?>

<div class="image-list">
	<?php if(empty($images)) : ?>
	<div class="alert alert-info"><a class="close" data-dismiss="alert" href="#">&times;</a> <?= $user['User']['username']; ?> does not yet have saved images.</div>
	<?php endif; ?>
	<ul class="row thumbnails">
	<?php foreach ($images as $image) : ?>
		<li class="span2 text-center">
			<div class="thumbnail">
				<?= $this->Html->link($this->Html->image($user_dir . '200/' . $image['Asset']['filename']),array('action'=>'view',$image['Asset']['id']),array('escape'=>false)); ?>
				<p class="date"><?= date('Y.m.d H:m:s',strtotime($image['Asset']['created'])); ?></p>
			</div>
		</li>
	<?php endforeach; ?>
	</ul>
</div>

<?= $this->element('admin/pagination'); ?>