<?php 
	$rel_path = IMAGES.$user_dir.$asset['Asset']['filename'];
	$specs = getimagesize($rel_path);
	$filesize = filesize($rel_path);
?>
<h1>Saved Image</h1>
<?//= $this->Html->link() ?>
<div class="span2 asset-view">
	<dl>
		<dt>Created on</dt> <dd><?= $asset['Asset']['created']; ?></dd>
		<dt>Dimensions</dt> <dd><?= $specs[0]; ?> px x <?= $specs[1]; ?> px (W x H)</dd>
		<dt>File Size</dt> <dd><?= round($filesize / 1024.0,2); ?> KBytes</dd>
		<dt>Type</dt> <dd><?= $specs['mime']; ?></dd>
	</dl>
	<h4>Actions</h4>
	<ul class="unstyled actions">
		<li><?= $this->Html->link('<i class="icon icon-chevron-left"></i> Return to List',array('action'=>'index'),array('class'=>'btn','escape'=>false)); ?></li>
		<li><?= $this->Html->link('<i class="icon-white icon-remove"></i> Delete Image',array('action'=>'delete',$asset['Asset']['id']),array('class'=>'btn btn-danger','escape'=>false),'Are you sure you wish to permanently delete this image?'); ?></li>
	</ul>	
</div>
<div class="span6">
	<?= $this->Html->image($user_dir.$asset['Asset']['filename']); ?>
</div>