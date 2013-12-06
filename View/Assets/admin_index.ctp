<?php
	$this->set('contentSpan',10);
?>	
<div class="row">
	<div class="col-md-2 text-right action-bar">
		<div class="row">
			<div class="col-xs-6 col-md-12">
				<ul class="list-unstyled actions">
					<li><?= $this->Html->link('<span class="glyphicon glyphicon-upload"></span> Upload Image',array('action'=>'upload'),array('class'=>'btn btn-success btn-block image-upload-btn','escape'=>false)); ?></li>
					<li><?= $this->Html->link('<span class="glyphicon glyphicon-camera"></span> Create Album',array('controller' => 'albums', 'action' => 'add'),array('class'=>'btn btn-default btn-block album-module-btn','escape'=>false)); ?></li>
				</ul>
			</div>
			<div class="col-xs-6 col-md-12">
				<ul class="list-unstyled">
					<li><strong><?= $this->Session->read('Auth.User.username'); ?></strong> <span class="glyphicon glyphicon-user"></span></li>
					<li><?= $image_total; ?> <span class="glyphicon glyphicon-picture"></span></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-md-10">
	
	<?php if(isset($album)) : ?>

		<h1><?= (!empty($album['Album']['title'])) ? $album['Album']['title'] : 'Unnamed album'; ?></h1>
		<?php if(!empty($album['Album']['description'])) : ?>
		<p><?= nl2br(h($album['Album']['description'])); ?></p>
		<?php endif; // description ?>

		<?php if(!empty($album['Album']['location'])) : ?>
		<p class="muted"><?= h($album['Album']['location']); ?></p>
		<?php endif; // description ?>

	<?php else: ?>

		<h1>Your Images</h1>

		<?php if(!empty($images)) : ?>
		<p class="tall">
			You have a total of <span class="badge <?= (count($images))?'badge-custom':''; ?>"><?= $image_total; ?></span> images
			<?php if(count($album_list)) : ?>
			and <span class="badge badge-custom"><?= count($album_list); ?></span> albums.
			<?php endif; ?>
		</p>
		<?php endif; ?>

	<?php endif; ?>

	<?php if(!empty($tag['Tag'])) : ?>

		<h3 class="cozy">
			Viewing Images in the Category: <span class="badge badge-info active-tag"><?= $tag['Tag']['name']; ?></span> 
			<?= $this->Html->link('Clear &times;', array('action' => $this->request->action), array('class' => 'badge badge-muted', 'escape' => false)); ?>
		</h3>

	<?php endif; //tag ?>

	<?php if(!empty($albums) && $this->Paginator->param('page') === 1) : ?>

	<h3 class="cozy-top">Recent Albums</h3>

		<ul class="media-list link-exchange">

		<?php foreach ($albums as $recent_album): ?>

			<li class="media">
				<div class="link-item clearfix">
					<a class="pull-left screenshot" href="<?= $this->Html->url(array('album' => $recent_album['Album']['id'])); ?>">
						<?php
							if(isset($recent_album['Cover']['image-thumb'])) {
								echo $this->Html->image($recent_album['Cover']['image-thumb']);
							} elseif (isset($recent_album['DefaultCover'][0]['image-thumb'])) {
								echo $this->Html->image($recent_album['DefaultCover'][0]['image-thumb']);
							}
						?>
					</a>
					<div class="media-body">
						<h5 class="media-heading"><?= $this->Html->link($recent_album['Album']['title'], array('album' => $recent_album['Album']['id']), array('class' => 'main')); ?></h5>
						<p class="description">
							<?= nl2br(h($recent_album['Album']['description'])); ?>
						</p>
						<h6>
							Contains <span class="badge badge-inverse"><strong><?= isset($recent_album['AssetCount'][0][0]['count']) ? $recent_album['AssetCount'][0][0]['count'] : 0 ; ?></strong></span>
							Images &nbsp;&bull;&nbsp;
							Created <?= $this->Time->timeAgoInWords($recent_album['Album']['created']); ?>
						</h6>
					</div>
				</div>
			</li>

		<?php endforeach; ?>

		</ul>

	<h3 class="cozy-top">Your Images</h3>

	<?php endif; // recent albums ?>
		
		<?= $this->element('admin/pagination'); ?>
		
		<div class="image-wall" data-role="taggable" data-model="Asset">
		<?php 
			foreach ($images as $image) {
				echo $this->Html->link($this->Html->image($image['Asset']['image-thumb']), array('action'=>'view',$image['Asset']['id']), array('data-id' => $image['Asset']['id'], 'escape'=>false));
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