<?php
	$this->set('contentSpan',10);
?>	
<div class="row-fluid">
	<div class="span2 text-right action-bar">
		<div class="extra">
			<h3>Contributing Users</h3>
			<ul class="unstyled">
				<?php foreach($contributingUsers as $user) : ?>
				<li><strong><?= $this->Html->link($user['User']['username'],array('action'=>'user', $user['User']['id']),array('escape'=>false)); ?></strong> <i class="icon-white icon-user"></i></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<div class="span10">

		<?php if(isset($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'Meme') : ?>

		<h1>Memes From All Users</h1>
		<p class="tall">We have a total of <span class="badge badge-custom"><?= $this->Paginator->counter('{:count}'); ?></span> memes saved.</p>

		<?php elseif (isset($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'Meme-Ready') : ?>

		<h1>Memes-Ready Images</h1>
		<p class="tall">We have a total of <span class="badge badge-custom"><?= $this->Paginator->counter('{:count}'); ?></span> images ready for the <?= $this->Html->link('Meme Generator', array('controller' => 'pages', 'action' => 'meme_generator')); ?>.</p>

		<?php else : ?>

		<h1>Images From All Users</h1>
		<p class="tall">We have a total of <span class="badge <?= (count($contributingUsers))?'badge-custom':''; ?>"><?= count($contributingUsers); ?></span> users contributing <span class="badge <?= (count($image_total))?'badge-custom':''; ?>"><?= $image_total; ?></span> images.</p>

		<?php endif; // various Index diffentiations ?>

		<?php if(!empty($tag['Tag'])) : ?>
		<h3 class="cozy">
			Viewing Images in the Category: <span class="badge badge-info active-tag"><?= $tag['Tag']['name']; ?></span> 
			<?= $this->Paginator->link('Clear &times;', array('tag' => false), array('class' => 'badge badge-muted', 'escape' => false)); ?>
		</h3>
		<?php endif; //tag ?>
		
		<?= $this->element('admin/pagination'); ?>

		<div class="image-wall" data-role="taggable" data-model="Asset">
		<?php 
			foreach ($images as $image) {
				echo $this->Html->link($this->Html->image($image['Asset']['image-thumb']),array('action'=>'view',$image['Asset']['id']),array('data-id' => $image['Asset']['id'], 'escape'=>false));
			} 
		?>
		</div>
		
		<?= $this->element('admin/pagination', array('show_summary' => true)); ?>
	</div>
</div>

<?php $this->element('common/tag-tally'); ?>

<?php
// optional quick-tagging mode
if(isset($this->request->params['named']['mode']) && $this->request->params['named']['mode'] == 'tag') {
	$this->append('sidebar-bottom');
	echo $this->element('common/quick-tagging');
	$this->end();
}
?>