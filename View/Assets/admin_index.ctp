<?php
	$this->set('contentSpan', 10);
	$this->set('title', 'My Images');
?>
<div class="row">
	<div class="col-md-2 text-right action-bar">
		<div class="row">
			<div class="col-xs-6 col-md-12">
				<ul class="list-unstyled actions">
					<li><?= $this->Html->link('<span class="glyphicon glyphicon-upload"></span> Upload Image', array('action' => 'upload'), array('class' => 'btn btn-success btn-block image-upload-btn', 'escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon glyphicon-camera"></span> ' . (isset($album['Album']['id']) ? 'Edit' : 'Create') . ' Album', array('controller' => 'albums', 'action' => 'save'), array('class' => 'btn btn-default btn-block album-module-btn', 'escape' => false)); ?></li>
					<?php if(isset($album['Album']['id'])) : ?>
					<li><?= $this->Form->postLink('<span class="glyphicon glyphicon-remove"></span> Delete Album', array('controller' => 'albums', 'action' => 'delete', $album['Album']['id']), array('class' => 'btn btn-block btn-xs btn-danger delete', 'escape' => false, 'title' => 'Delete this image'), 'Are you sure you wish to delete this album? It will not delete the photos in it.'); ?></li>
					<?php endif; ?>
				</ul>
			</div>
			<div class="col-xs-6 col-md-12">
				<ul class="list-unstyled">
					<li><strong><?= $this->Session->read('Auth.User.username'); ?></strong> <span class="glyphicon glyphicon-user"></span></li>
					<li><?= $this->Paginator->param('count'); ?> <span class="glyphicon glyphicon-picture"></span></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-md-10">

	<?php if(isset($album)) :
		$this->set('title', htmlentities($album['Album']['title'] . ' - Album'));
	?>

		<?= $this->element('../Albums/_album_overview', array('album' => $album)); ?>

		<?php if($this->Paginator->param('count') === 0) : ?>
		<div class="alert alert-info">This album is currently empty, you can <a class="image-upload-btn" href="#"><strong>upload new images</strong></a> or assign existing images to this album.</div>
		<?php endif; ?>

	<?php else: ?>

		<h1>My Images</h1>

		<?php if(!empty($images)) : ?>
		<p class="tall">
			You have a total of <span class="badge <?= (count($images))?'badge-custom':''; ?>"><?= $this->Paginator->param('count'); ?></span> images
			<?php if($album_count > 0) : ?>
			and <span class="badge badge-custom"><?= $album_count; ?></span> albums.
			<?php endif; ?>
		</p>
		<?php endif; ?>

	<?php endif; ?>

	<?php if(!empty($tag['Tag'])) : ?>

		<h3 class="cozy">
			Viewing Images with the Tag: <span class="badge badge-info active-tag"><?= $tag['Tag']['name']; ?></span>
			<?= $this->Paginator->link('Clear &times;', array('tag' => false), array('class' => 'badge badge-muted', 'escape' => false)); ?>
		</h3>

	<?php endif; //tag ?>

	<?php if(!empty($albums) && $this->Paginator->param('page') === 1 && empty($this->request->params['named'])) : ?>

	<h3 class="cozy-top">Recent <?= (count($albums) === 1) ? 'Album' : 'Albums'; ?></h3>

	<?php if($album_count > count($albums)) : ?>
	<p>
		<?= $this->Html->link('View All My Albums <span class="glyphicon glyphicon-chevron-right"></span>', array('action' => 'albums', 'user' => $this->Session->read('Auth.User.id')), array('escape' => false)); ?>
	</p>
	<?php endif; // view all albums ?>

	<ul class="media-list link-exchange">

	<?php foreach ($albums as $recent_album) : ?>
		<li class="media"><?= $this->element('../Albums/_album_item', array('album' => $recent_album)); ?></li>
	<?php endforeach; ?>

	</ul>

	<h3 class="cozy-top">My Images</h3>

	<?php endif; // recent albums ?>

		<?= $this->element('admin/pagination'); ?>

		<div class="image-wall" data-role="taggable" data-model="Asset">
		<?php
			foreach ($images as $image) {
				echo $this->Html->link($this->Html->image($image['Asset']['image-thumb']), array('action' => 'view', $image['Asset']['id'], '#' => 'image'), array('data-id' => $image['Asset']['id'], 'escape' => false));
			}
		?>
		</div>

		<?= $this->element('admin/pagination', array('show_summary' => true)); ?>
	</div>
</div>

<?php $this->element('common/tag-tally'); ?>

<?php
// optional quick-tagging mode
if(isset($this->request->params['named']['mode']) && $this->request->params['named']['mode'] == 'tag' && !$this->request->is('mobile')) {
	$this->append('sidebar-bottom');
	echo $this->element('common/quick-tagging');
	$this->end();
}
?>

<?php $this->append('sidebar-bottom'); ?>
<div class="tips">

<?php if(!$this->request->is('mobile')): ?>
	<p><span class="glyphicon glyphicon-info-sign"></span> You can upload new images by dragging and dropping them onto the browser, on any page.</p>
<?php endif; ?>

</div>
<?php $this->end(); ?>

<?= $this->element('common/album-module'); ?>