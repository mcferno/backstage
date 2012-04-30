<h1>Your Images</h1>

<?php if(!empty($images)) : ?>
<p>The following images have been saved under your account.</p>
<?php endif; ?>


<?= $this->element('admin/pagination',array('show_summary'=>true)); ?>

<div class="image-list">
	<?php if(empty($images)) : ?>
	<div class="alert alert-info"><a class="close" data-dismiss="alert" href="#">&times;</a> No images saved.</div>
	<?php endif; ?>
	<ul class="row thumbnails">
	<?php 
		$webroot = WWW_ROOT.'img/';
		foreach ($images as $image) : 
	?>
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