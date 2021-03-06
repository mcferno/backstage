<?php
	$this->set('contentSpan', 10);
?>
<div class="row">
	<div class="col-md-2 text-right action-bar">

		<?php if(isset($this->request->params['named']['album'])) : ?>

		<div class="row">
			<div class="col-xs-6 col-md-12">
				<ul class="list-unstyled actions">
					<li><?= $this->Html->link('<span class="glyphicon glyphicon-upload"></span> Upload Image', array('action' => 'upload'), array('class' => 'btn btn-success btn-block image-upload-btn', 'escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon glyphicon-camera"></span> ' . (isset($album['Album']['id']) ? 'Edit' : 'Create') . ' Album', array('controller' => 'albums', 'action' => 'save'), array('class' => 'btn btn-default btn-block album-module-btn', 'escape' => false)); ?></li>
				</ul>
			</div>
		</div>

		<?php else : ?>

		<div class="extra">
			<h3>Contributing Users</h3>
			<ul class="list-unstyled">
				<?php foreach($contributingUsers as $user) : ?>
				<li><strong><?= $this->Html->link($user['User']['username'], array('action' => 'user', $user['User']['id']), array('escape' => false)); ?></strong> <span class="glyphicon glyphicon-user"></span></li>
				<?php endforeach; ?>
			</ul>
		</div>

		<?php endif; ?>

	</div>
	<div class="col-md-10">

		<?php if(isset($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'Meme') :
			$this->set('title', 'All Memes');
		?>

		<h1>Memes From All Users</h1>
		<p class="tall">We have a total of <span class="badge badge-custom"><?= $this->Paginator->counter('{:count}'); ?></span> memes saved.</p>

		<?php elseif (isset($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'Meme-Templates') :
			$this->set('title', 'Meme Templates');
		?>

		<h1>Meme Templates <small>(with no text)</small></h1>
		<p class="tall">We have a total of <span class="badge badge-custom"><?= $this->Paginator->counter('{:count}'); ?></span> images ready for the <?= $this->Html->link('Meme Generator', array('controller' => 'pages', 'action' => 'meme_generator')); ?>.</p>

		<?php elseif(isset($this->request->params['named']['album']) && !empty($album['Album']['id'])) :
			$this->set('title', htmlentities($album['Album']['title'] . ' - Album'));
		?>

		<?= $this->element('../Albums/_album_overview', array('album' => $album)); ?>

		<?php else :
			$this->set('title', 'All Images');
		?>

		<h1>Images From All Users</h1>
		<p class="tall">We have a total of <span class="badge <?= (count($contributingUsers))?'badge-custom':''; ?>"><?= count($contributingUsers); ?></span> users contributing <span class="badge <?= (count($image_total))?'badge-custom':''; ?>"><?= $image_total; ?></span> images.</p>

		<?php endif; // various Index diffentiations ?>

		<?php if(!empty($tag['Tag'])) : ?>
		<h3 class="cozy">
			Viewing Images with the Tag: <span class="badge badge-info active-tag"><?= $tag['Tag']['name']; ?></span>
			<?= $this->Paginator->link('Clear &times;', array('tag' => false), array('class' => 'badge badge-muted', 'escape' => false)); ?>
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