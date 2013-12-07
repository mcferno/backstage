<?php
	$this->set('contentSpan',10);
?>	
<div class="row">
	<div class="col-md-2 text-right action-bar">
		<div class="row">
			<div class="col-xs-6 col-md-12">
				<ul class="list-unstyled actions">
					<li><?= $this->Html->link('<span class="glyphicon glyphicon-upload"></span> Upload Image',array('action'=>'upload'),array('class'=>'btn btn-success btn-block image-upload-btn','escape' => false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon glyphicon-camera"></span> ' . (isset($album['Album']['id']) ? 'Edit' : 'Create') . ' Album',array('controller' => 'albums', 'action' => 'save'),array('class'=>'btn btn-default btn-block album-module-btn','escape' => false)); ?></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-md-10">

		<?php
			$title = 'All Albums';
			if(isset($this->request->params['named']['user'])) {
				if(Access::isOwner($this->request->params['named']['user'])) {
					$title = 'My Albums';
				} elseif(isset($users[$this->request->params['named']['user']])) {
					$title = $users[$this->request->params['named']['user']] . '’s Albums';
				} else {
					$title = 'All Albums from a User';
				}
			}

			echo $this->Html->tag('h1', $title);
		?>

		<?= $this->element('admin/pagination'); ?>

			<ul class="media-list link-exchange">

			<?php foreach ($albums as $album) : ?>
				<li class="media"><?= $this->element('../Albums/_album_item', array('album' => $album)); ?></li>
			<?php endforeach; unset($album); ?>

			</ul>

		<?= $this->element('admin/pagination', array('show_summary' => true)); ?>
	</div>
</div>

<?= $this->element('common/album-module'); ?>