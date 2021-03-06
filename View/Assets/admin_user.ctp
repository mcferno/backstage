<?php $this->set('title', htmlentities("{$user['User']['username']}’s Images")); ?>
<h1><?= $user['User']['username']; ?>’s Images</h1>
<p class="tall"><?= $user['User']['username']; ?> has a total of <span class="badge <?= (count($images))?'badge-custom':''; ?>"><?= $image_total; ?></span> images</p>

<?php if(!empty($tag['Tag'])) : ?>
<h3 class="cozy">
	Viewing Images with the Tag: <span class="badge badge-info active-tag"><?= $tag['Tag']['name']; ?></span>
	<?= $this->Html->link('Clear &times;', array('action' => $this->request->action, $user['User']['id']), array('class' => 'badge badge-muted', 'escape' => false)); ?>
</h3>
<?php endif; //tag ?>

<div class="image-wall" data-role="taggable" data-model="Asset">
<?php
	foreach ($images as $image) {
		echo $this->Snippet->assetThumbnail($image);
	}
?>
</div>

<?= $this->element('admin/pagination', array('show_summary' => true)); ?>

<?php $this->element('common/tag-tally'); ?>

<?php
// optional quick-tagging mode
if(isset($this->request->params['named']['mode']) && $this->request->params['named']['mode'] == 'tag' && !$this->request->is('mobile')) {
	$this->append('sidebar-bottom');
	echo $this->element('common/quick-tagging');
	$this->end();
}
?>