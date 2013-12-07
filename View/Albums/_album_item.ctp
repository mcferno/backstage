
<?php
$link_action = (!empty($album['Album']['user_id']) && Access::isOwner($album['Album']['user_id'])) ? 'index' : 'users';
?>
<div class="link-item clearfix">
	<a class="pull-left screenshot" href="<?= $this->Html->url(array('action' => $link_action, 'album' => $album['Album']['id'])); ?>">
		<?php
			if(isset($album['Cover']['image-thumb'])) {
				echo $this->Html->image($album['Cover']['image-thumb']);
			} elseif (isset($album['DefaultCover'][0]['image-thumb'])) {
				echo $this->Html->image($album['DefaultCover'][0]['image-thumb']);
			}
		?>
	</a>
	<div class="media-body">
		<h5 class="media-heading"><?= $this->Html->link($album['Album']['title'], array('action' => $link_action, 'album' => $album['Album']['id']), array('class' => 'main')); ?></h5>

		<?php if(trim($album['Album']['description']) != '') : ?>
		<p class="description">
			<?= nl2br(h($album['Album']['description'])); ?>
		</p>
		<?php endif; ?>

		<?php if(!empty($album['Album']['location'])) : ?>
		<p class="muted">&mdash; <?= h($album['Album']['location']); ?></p>
		<?php endif; // description ?>

		<?php if(!empty($album['Asset'])) : ?>
		<p>
		<?php
			foreach ($album['Asset'] as $asset) {
				echo $this->Html->link($this->Html->image($asset['image-tiny']), array('action' => $link_action, 'album' => $album['Album']['id']), array('escape' => false));
			}
		?>
		</p>
		<?php endif; ?>

		<h6>
			Contains <span class="badge badge-inverse"><strong><?= isset($album['AssetCount'][0][0]['count']) ? $album['AssetCount'][0][0]['count'] : 0 ; ?></strong></span>
			Images &nbsp;&bull;&nbsp;
			Created <?= $this->Time->timeAgoInWords($album['Album']['created']); ?>
			<?php if(!empty($album['User'])) : ?>

			&nbsp;&bull;&nbsp; by <span class="glyphicon-user glyphicon" title="Online Users"></span> <?= $this->Html->link($album['User']['username'], array('user' => $album['Album']['user_id']), array('escape' => false)); ?>
			<?php endif; ?>
		</h6>
	</div>
</div>
