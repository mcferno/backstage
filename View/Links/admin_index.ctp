<?php
	$this->set('contentSpan', 10);
?>
<div class="row-fluid">
	<div class="span2 text-right action-bar">
		<h3>Actions</h3>
		<ul class="unstyled actions">
			<li><?= $this->Html->link('<i class="icon-white icon-pencil"></i> Add a Link', array('controller' => 'links', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?></li>
			<li>
				<div class="dropdown">
					<a class="dropdown-toggle btn btn" data-toggle="dropdown" href="#"><i class="icon icon-random"></i> Sort Links by</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
							<li><?php echo $this->Paginator->sort('created', 'Date Submitted <i class="icon-white icon-time"></i>', array('direction' => 'desc', 'escape'=>false)); ?></li>
							<li><?php echo $this->Paginator->sort('title', 'Link Name <i class="icon-white icon-comment"></i>', array('escape'=>false)); ?></li>
							<li><?php echo $this->Paginator->sort('url', 'URL <i class="icon-white icon-share-alt"></i>', array('escape'=>false)); ?></li>
					</ul>
				</div>
			</li>
		</ul>
	</div>

	<div class="span10">
		<h1>Link Exchange</h1>
		<p>Got some links others need to know about? Just want to browse a collection of the best content? This is the place.</p>

		<?= $this->element('admin/pagination'); ?>

		<ul class="link-exchange unstyled striped">
		<?php foreach ($links as $link): ?>
		<li>
			<div class="title">
				<a href="<?= $link['Link']['url']; ?>" target="_blank" class="main"><?= $link['Link']['title']; ?></a> <span class="muted">« <a href="<?= $link['Link']['url']; ?>" target="_blank"><?= $link['Link']['url']; ?></a> »</span>
			</div>
			<div class="description">
			<?php
				if(empty($link['Link']['description'])) {
					echo $this->Html->tag('span', 'no description', array('class' => 'muted')); 
				} else {
					echo $link['Link']['description'];
				}
			?>
			</div>
			<div class="stats muted">
				&mdash; posted <?= date('M j', strtotime($link['Link']['created'])); ?> by <strong><?= $link['User']['username']; ?></strong> <small>(<?= $this->Time->timeAgoInWords($link['Link']['created'], array('end' => '+1 year', 'accuracy' => array('month' => 'month'))); ?>)</small>
			</div>
			<div class="interact">
				<?= $this->Html->link('<span class="badge badge-success comments">3 comments</span>', array('action' => 'view', $link['Link']['id']), array('escape' => false)); ?>
				<span class="badge badge-info">Game</span>
				<span class="badge badge-pale">HTML5</span>
				<span class="badge badge-info">Chrome</span>
				<span class="badge badge-pale">Audio</span>

				<?php if($this->Session->read('Auth.User.id') == $link['Link']['user_id'] || (int)$this->Session->read('Auth.User.role') >= ROLE_ADMIN) : ?>
				<div class="controls" style="display:none;">
					<?= $this->Html->link($this->Html->image('ui/icons/pencil.png') . ' Edit', array('action' => 'edit', $link['Link']['id']), array('class' => 'btn btn-inverse edit-btn', 'title' => 'Edit this link', 'escape' => false)); ?>
					<?= $this->Form->postLink($this->Html->image('ui/icons/prohibition.png') . ' Delete', array('action' => 'delete', $link['Link']['id']), array('class' => 'btn btn-inverse', 'title' => 'Delete this link', 'escape' => false), "Are you sure you want to delete the {$link['Link']['title']} link?"); ?>
				</div>
				<?php endif; ?>
			</div>
		</li>
		<?php endforeach; ?>
		</ul>

		<?= $this->element('admin/pagination'); ?>
	</div>
</div>