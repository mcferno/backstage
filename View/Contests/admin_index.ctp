<?php
	$this->set('contentSpan',10);
?>	
<div class="row">
	<div class="col-md-2 action-bar">
		&nbsp;
		<?php if(!empty($activeContests)) : ?>
		<p><span class="glyphicon glyphicon-info-sign"></span> To start a caption battle of your own, pick an image from <?= $this->Html->link('Your Images',array('controller'=>'assets','action'=>'index'),array('escape'=>false)); ?>.</p>
		<?php endif; ?>
	</div>
	<div class="col-md-10">
		<div class="page-header">
			<h1>Caption Battles <?php if(!empty($activeContests)) : ?><small>Active Battles</small><?php endif; ?></h1>
		</div>

		<?php if(!empty($activeContests)) : ?>
		<div class="active-contests">
			<div class="row">
				<?php foreach ($activeContests as $idx => $contest) : ?>
				<div class="col-xs-6 col-sm-4 col-lg-3 contest">
					<?= $this->Html->link($this->Html->image($contest['Asset']['image-thumb']),array('action'=>'view',$contest['Contest']['id']),array('escape'=>false, 'class' => '')); ?>
					<div class="caption">
						<dl>
							<dt>Started by</dt><dd><span class="glyphicon-user glyphicon"></span> <?= $contest['User']['username']; ?> on <?= date('l, F jS', strtotime($contest['Contest']['created'])); ?></dd>
						<?php if(!empty($contest['Contest']['message'])) : ?>
							<dt>Description</dt><dd><?= $contest['Contest']['message']; ?></dd>
						<?php endif; ?>
						</dl>
						<?= $this->Html->link('<span class="glyphicon glyphicon-pencil"></span> Add Caption',array('controller'=>'pages', 'action' => 'meme_generator', 'contest' => $contest['Contest']['id']),array('class'=>'btn btn-block btn-success','escape'=>false)); ?>
						<?= $this->Html->link('<span class="glyphicon glyphicon-search"></span> View Entries',array('action'=>'view', $contest['Contest']['id']),array('class'=>'btn btn-block btn-primary','escape'=>false)); ?>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>

		<?php else : ?>

		<h3 class="alert alert-info">There are no active battles. To start one, pick an image from <?= $this->Html->link('Your Images',array('controller'=>'assets','action'=>'index'),array('escape'=>false)); ?>.</h3>

		<?php endif; ?>

		<div class="page-header cozy-lead">
			<h2>Past Battles
			<?php if(!empty($activeContests)) : ?>
			<small>
				There have been 
					<span class="badge <?= (count($contests))?'badge-custom':''; ?>">
						<?= $this->Paginator->counter(array('format' =>'{:count}')); ?>
					</span>
				battles.
			</small>
			<?php endif; ?></h2>
		</div>

		<div class="image-list">
			<?php if(empty($contests)) : ?>
			<div class="alert alert-info"><a class="close" data-dismiss="alert" href="#">&times;</a> No images saved.</div>
			<?php endif; ?>
			<div class="image-wall">
			<?php foreach ($contests as $contest) : ?>
				<?= $this->Html->link($this->Html->image($contest['Asset']['image-thumb']),array('action'=>'view',$contest['Contest']['id']),array('escape'=>false)); ?>
			<?php endforeach; ?>
			</div>
		</div>
		
	</div>
</div>