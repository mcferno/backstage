<?php
	$this->set('contentSpan',10);
?>	
<div class="row-fluid">
	<div class="span2">
		&nbsp;
		<?php if(!empty($activeContests)) : ?>
		<p><i class="icon-white icon-info-sign"></i> To start a caption battle of your own, pick an image from <?= $this->Html->link('Your Images',array('controller'=>'assets','action'=>'index'),array('escape'=>false)); ?>.</p>
		<?php endif; ?>
	</div>
	<div class="span10">
		<h1>Caption Battles</h1>
		<p>There have been <span class="badge <?= (count($contests))?'badge-custom':''; ?>"><?= $this->Paginator->counter(array('format' =>'{:count}')); ?></span> battles.<p>

		<?php if(!empty($activeContests)) : ?>
		<div class="active-contests">
			<h2>Active Battles!</h2>

			<ul class="row thumbnails">
				<?php foreach ($activeContests as $contest) : ?>
				<li class="span4">
					<div class="thumbnail text-center">
						<?= $this->Html->link($this->Html->image("user/{$contest['User']['id']}/200/{$contest['Asset']['filename']}"),array('action'=>'view',$contest['Contest']['id']),array('escape'=>false, 'class' => '')); ?>
						<dl class="text-left">
							<dt>Started by</dt><dd><i class="icon-user icon-white"></i> <?= $contest['User']['username']; ?> on <?= date('l, F jS', strtotime($contest['Contest']['created'])); ?></dd>
						<?php if(!empty($contest['Contest']['message'])) : ?>
							<dt>Description</dt><dd><?= $contest['Contest']['message']; ?></dd>
						<?php endif; ?>
						</dl>
						<p>
							<?= $this->Html->link('<i class="icon-white icon-pencil"></i> Add Caption',array('controller'=>'pages', 'action' => 'meme_generator', 'contest' => $contest['Contest']['id']),array('class'=>'btn btn-large btn-success','escape'=>false)); ?>
							&nbsp;
							<?= $this->Html->link('<i class="icon-white icon-search"></i> View Entries',array('action'=>'view', $contest['Contest']['id']),array('class'=>'btn btn-large btn-primary','escape'=>false)); ?>
						</p>
					</div>
				</li>
				<?php endforeach; ?>

			</ul>
		</div>

		<hr>

		<?php else : ?>

		<h3 class="alert alert-info">There are no active battles. To start one, pick an image from <?= $this->Html->link('Your Images',array('controller'=>'assets','action'=>'index'),array('escape'=>false)); ?>.</h3>

		<?php endif; ?>
		
		<h2>All Battles</h2>

		<?= $this->element('admin/pagination',array('show_summary'=>true)); ?>
		
		<div class="image-list">
			<?php if(empty($contests)) : ?>
			<div class="alert alert-info"><a class="close" data-dismiss="alert" href="#">&times;</a> No images saved.</div>
			<?php endif; ?>
			<ul class="row thumbnails">
			<?php foreach ($contests as $contest) : ?>
				<li class="span2 text-center">
					<div class="thumbnail">
						<?= $this->Html->link($this->Html->image("user/{$contest['User']['id']}/200/{$contest['Asset']['filename']}"),array('action'=>'view',$contest['Contest']['id']),array('escape'=>false)); ?>
						<p>Started by <strong><?= $contest['User']['username']; ?></strong></p>
						<p class="date"><?= date('Y.m.d H:m:s',strtotime($contest['Contest']['created'])); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
		
		<?= $this->element('admin/pagination'); ?>
	</div>
</div>