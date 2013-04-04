<?php
	$this->set('contentSpan',10);
?>	
<div class="row-fluid">
	<div class="span2 text-right action-bar">
		<h3>Specs</h3>
		<ul class="unstyled">
			<li><strong><?= $this->Session->read('Auth.User.username'); ?></strong> <i class="icon-white icon-user"></i></li>
			<li><?= $image_total; ?> <i class="icon-white icon-picture"></i></li>
		</ul>
		<h3>Actions</h3>
		<ul class="unstyled actions">
			<li><?= $this->Html->link('<i class="icon-white icon-upload"></i> Upload Image',array('action'=>'upload'),array('class'=>'btn btn-success image-upload-btn','escape'=>false)); ?></li>
		</ul>
	</div>
	<div class="span10">
		<h1>Your Images</h1>
		<?php if(!empty($images)) : ?>
		<p class="tall">You have a total of <span class="badge <?= (count($images))?'badge-custom':''; ?>"><?= $image_total; ?></span> images</p>
		<?php endif; ?>

		<?php if(!empty($tag['Tag'])) : ?>
		<h3 class="cozy">
			Viewing Images in the Category: <span class="badge badge-info active-tag"><?= $tag['Tag']['name']; ?></span> 
			<?= $this->Html->link('Clear &times;', array('action' => $this->request->action), array('class' => 'badge badge-muted', 'escape' => false)); ?>
		</h3>
		<?php endif; //tag ?>
		
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
	<p><i class="icon-white icon-info-sign"></i> You can upload new images by dragging and dropping them onto the browser, on any page.</p>
<?php endif; ?>

</div>
<?php $this->end(); ?>