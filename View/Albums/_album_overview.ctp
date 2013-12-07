<h1><?= (!empty($album['Album']['title'])) ? $album['Album']['title'] : 'Unnamed album'; ?></h1>
<?php if(!empty($album['Album']['description'])) : ?>
<p><?= nl2br(h($album['Album']['description'])); ?></p>
<?php endif; // description ?>

<?php if(!empty($album['Album']['location'])) : ?>
<p class="muted">&mdash; <?= h($album['Album']['location']); ?></p>
<?php endif; // description ?>

<h6>
	Contains <span class="badge badge-inverse"><strong><?= isset($album['AssetCount'][0][0]['count']) ? $album['AssetCount'][0][0]['count'] : 0 ; ?></strong></span>
	Images &nbsp;&bull;&nbsp;
	Created <?= $this->Time->timeAgoInWords($album['Album']['created']); ?>
	<?php if(!empty($album['User'])) : ?>

	&nbsp;&bull;&nbsp; by <span class="glyphicon-user glyphicon" title="Online Users"></span> <?= $this->Html->link($album['User']['username'], array('user' => $album['Album']['user_id']), array('escape' => false)); ?>
	<?php endif; ?>
</h6>