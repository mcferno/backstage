<?php
	$this->set('contentSpan',10);
	$landing_page = (empty($this->request->params['named']['page']));
?>	
<div class="row">
	<div class="span12">
		<h1>Caption Battle</h1>
	</div>
</div>
<div class="row">
	<div class="span2 text-right">
		<ul class="unstyled">
			<li><strong><?= $contest['User']['username']; ?></strong> <i class="icon-white icon-user"></i></li>
			<li><?= $contest['Contest']['created']; ?> <i class="icon-white icon-time"></i></li>
			<li><span class="badge badge-custom"><?= ($landing_page) ? count($assets) : (int)$this->Paginator->counter('{:count}'); ?></span> total entries <i class="icon-white icon-folder-open"></i></li>
			<?php if(!$landing_page && !empty($contest['Contest']['message'])) : ?>
			<li class="comment"><i class="icon-white icon-comment pull-right"></i> <div class="text">&ldquo; <?= nl2br($contest['Contest']['message']); ?> &rdquo;</div></li>
			<?php endif; ?>
			
		</ul>
		<?php if(!$landing_page) : ?>

		<p><strong>Original</strong><br><?= $this->Html->image('user/' . $contest['User']['id'] . "/200/" .$contest['Asset']['filename'], array('url' => array('action' => 'view', $contest['Contest']['id']), 'title' => 'Caption Battle overview')); ?></p>

		<?php endif; ?>

		<h3>Actions</h3>
		<ul class="unstyled actions">
			<li><?= $this->Html->link('<i class="icon-white icon-pencil"></i> Add Caption', array('controller'=>'pages', 'action' => 'meme_generator', 'contest' => $contest['Contest']['id']),array('class'=>'btn btn-large btn-success','escape'=>false)); ?></li>
			<li><?= $this->Html->link('<i class="icon icon-chevron-left"></i> View Caption Battles', array('action'=>'index'),array('class'=>'btn','escape'=>false)); ?></li>
		</ul>
	</div>
	<div class="span8">

		<?php if($landing_page) : ?>

		<?php if(!empty($contest['Contest']['message'])) : ?>
		<h2><?= nl2br($contest['Contest']['message']); ?></h2>
		<?php endif; ?>

		<p><?= $this->Html->image('user/' . $contest['User']['id'] . '/' .$contest['Asset']['filename']); ?></p>

		<?php if(empty($assets)) : ?>

		<p class="alert alert-info">This caption battle has no entries yet, <?= $this->Html->link('add your own entry!', array('action' => 'view', $contest['Contest']['id'])); ?></p>

		<?php else : // contest has entries ?>

		<h2>Entries</h2>

		<ul class="row thumbnails">
		<?php 
		$page = 0;
		foreach ($assets as $asset) : $page++; ?>
			<li class="span2 text-center">
				<div class="thumbnail">
					<?= $this->Html->link($this->Html->image("user/{$asset['User']['id']}/200/{$asset['Asset']['filename']}"),array('action'=>'view',$contest['Contest']['id'], 'page'=> $page),array('escape'=>false)); ?>
					<p>by <strong><?= $asset['User']['username']; ?></strong></p>
					<p class="date"><?= date('Y.m.d H:m:s',strtotime($asset['Asset']['created'])); ?></p>
				</div>
			</li>
		<?php endforeach; ?>
		</ul>

		<?php endif; ?>

		<?php else : // single-entry viewing page ?>

		<?= $this->element('admin/pagination'); ?>

		<p>Entry by <i class="icon-white icon-user"></i> <strong><?= $assets[0]['User']['username']; ?></strong> on <i class="icon-white icon-time"></i> <?= $assets[0]['Asset']['created']; ?></p>
		<?= $this->Html->image('user/' . $assets[0]['User']['id'] . '/' .$assets[0]['Asset']['filename']); ?>

		<br><br>
		<?= $this->element('admin/pagination'); ?>

		<?php endif; ?>

	</div>
</div>