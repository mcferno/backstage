<?php
	$this->set('contentSpan',10);
	$landing_page = (empty($this->request->params['named']['page']));
?>	
<div class="row">
	<div class="col-md-12">
		<h1>Caption Battle</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-2 text-right action-bar">
		<ul class="unstyled">
			<li><strong><?= $contest['User']['username']; ?></strong> <span class="glyphicon glyphicon-user"></span></li>
			<li><?= $contest['Contest']['created']; ?> <span class="glyphicon glyphicon-time"></span></li>
			<li><span class="badge badge-custom"><?= ($landing_page) ? count($assets) : (int)$this->Paginator->counter('{:count}'); ?></span> total entries <span class="glyphicon glyphicon-folder-open"></span></li>
			<?php if(!$landing_page && !empty($contest['Contest']['message'])) : ?>
			<li class="comment"><span class="glyphicon glyphicon-comment pull-right"></span> <div class="text">&ldquo; <?= nl2br($contest['Contest']['message']); ?> &rdquo;</div></li>
			<?php endif; ?>
		</ul>

		<?php if(!empty($contest['Contest']['winning_asset_id'])) : ?>
		<p><strong>Winner</strong><br><?= $this->Html->image($contest['Winner']['image-thumb'], array('url' => array('action' => 'view', $contest['Contest']['id'], 'page' => 1), 'title' => 'Caption Battle overview')); ?></p>
		<?php endif; ?>

		<?php if(!$landing_page) : ?>

		<p><strong>Original</strong><br><?= $this->Html->image($contest['Asset']['image-thumb'], array('url' => array('action' => 'view', $contest['Contest']['id']), 'title' => 'Caption Battle overview')); ?></p>

		<?php if(empty($contest['Contest']['winning_asset_id']) && !empty($assets) && Access::isOwner($contest['Contest']['user_id'])) : ?>

		<h3>Contest Admin</h3>
		<?php
			$winner_confirm = "Do you wish to choose the current caption by {$assets[0]['User']['username']} as the Caption Battle Champion? This will end the contest and halt any further submissions.";
		?>
		<ul class="unstyled actions">
			<li><?= $this->Html->link('<span class="glyphicon glyphicon-star"></span> Declare as Winner', array('controller'=>'contests', 'action' => 'set_winner', $contest['Contest']['id'], $assets[0]['Asset']['id']),array('class'=>'btn btn-primary','escape'=>false), $winner_confirm); ?></li>
		</ul>

		<?php endif; // admin tools for open contest ?>
		<?php endif; // non-landing page ?>

		<h3>Actions</h3>
		<ul class="unstyled actions">
			<?php if(empty($contest['Contest']['winning_asset_id'])) : ?>
			<li><?= $this->Html->link('<span class="glyphicon glyphicon-pencil"></span> Add Caption', array('controller'=>'pages', 'action' => 'meme_generator', 'contest' => $contest['Contest']['id']),array('class'=>'btn btn-large btn-success','escape'=>false)); ?></li>
			<?php endif; ?>
			<li><?= $this->Html->link('<span class="glyphicon glyphicon-chevron-left"></span> View Caption Battles', array('action'=>'index'),array('class'=>'btn','escape'=>false)); ?></li>
		</ul>
	</div>
	<div class="col-md-10">

		<?php if($landing_page) : ?>

		<div class="text-center">

			<?php if(!empty($contest['Contest']['message'])) : ?>
			<h2><?= nl2br($contest['Contest']['message']); ?></h2>
			<?php endif; ?>

			<p><?= $this->Html->image($contest['Asset']['image-full'], array('style' => 'max-height: 500px;')); ?></p>
		</div>

		<?php if(empty($assets)) : ?>

		<p class="alert alert-info">This caption battle has no entries yet, <?= $this->Html->link('add your own entry!', array('controller'=>'pages', 'action' => 'meme_generator', 'contest' => $contest['Contest']['id'])); ?></p>

		<?php else : // contest has entries ?>

		<h2>Entries</h2>

		<ul class="row thumbnails">
		<?php 
		$page = 0;
		foreach ($assets as $asset) : 
			$page++; 
			$winner = ($contest['Contest']['winning_asset_id'] === $asset['Asset']['id']); ?>

			<li class="col-md-2 text-center">
				<div class="thumbnail">
					<?= $this->Html->link($this->Html->image($asset['Asset']['image-thumb']),array('action'=>'view',$contest['Contest']['id'], 'page'=> $page),array('escape'=>false)); ?>
					<p>
						<?php if($winner) { echo $this->Html->image('ui/icons/trophy.png', array('title' => 'Winning Entry!')); } ?> 
						by <strong><?= $asset['User']['username']; ?></strong>
					</p>
					<p class="date"><?= date('Y.m.d H:m:s',strtotime($asset['Asset']['created'])); ?></p>
				</div>
			</li>

		<?php endforeach; ?>
		</ul>

		<?php endif; // contest index with entries ?>

		<h3 class="text-right"><?= $this->Html->image('ui/icons/balloon.png'); ?> Comments</h3>
		<?= $this->element('common/chat-module', array('model' => 'Contest', 'foreign_key' => $contest['Contest']['id'])); ?>

		<?php else : // single-entry viewing page ?>

		<?= $this->element('admin/pagination'); ?>

		<p class="text-center">
		<?php if($contest['Contest']['winning_asset_id'] === $assets[0]['Asset']['id']) {
			echo $this->Html->image('ui/icons/trophy.png') . " <strong>Winning</strong> ";
		}
		?>
			Entry by <span class="glyphicon glyphicon-user"></span> <strong><?= $assets[0]['User']['username']; ?></strong> on <span class="glyphicon glyphicon-time"></span> <?= $assets[0]['Asset']['created']; ?>
		</p>
		<p class="text-center"><?= $this->Html->image($assets[0]['Asset']['image-full']); ?></p>

		<p class="text-center">Direct URL to Image<br><input type="text" class="col-md-4 copier" value="<?= FULL_BASE_URL . $this->Html->webroot(IMAGES_URL . $assets[0]['Asset']['image-full']); ?>"></p>

		<?= $this->element('admin/pagination', array('show_summary' => true)); ?>

		<h3 class="text-right"><?= $this->Html->image('ui/icons/balloon.png'); ?> Comments</h3>
		<?= $this->element('common/chat-module', array('model' => 'Asset', 'foreign_key' => $assets[0]['Asset']['id'])); ?>

		<?php endif; ?>



	</div>
</div>